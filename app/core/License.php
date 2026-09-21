<?php
// app/core/License.php

class License {

    const PUBLIC_KEY = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAt9C7YR2fqwZM7fSmwc/p
+rKBDjixp176WFDcLMU4jfs82QwhXuvbJiB9T2MMzph46GW1tyznymIValoYyXpE
/f7o4d1uv3oYixKZ40JYAlUpygkLeVdkfP2DS2hwCZSx5U3c01TdcDXHBMjkRO/k
pM88kgAPmq3xeRqU1H20L1Kg5zzrnp3ULxLC1jj+x9PPCljXTqjN6plST1YFi2DZ
zQGJw4ImDtq27wICMaaep8BYbastipCrgzEWZmGFJtWcGohFJqBB3Ly17dCyruuDd
pS4PqA/yJ3eR5r7RBJ5tZv8LvEZ/EBJSFSIGegm+WKtVQloLJtoy+V9aMUX1Vl4c
6wIDAQAB
-----END PUBLIC KEY-----
EOD;

    private static $jsonFile  = '../config/licencia.json';
    private static $keyFile   = '../config/license.key';
    private static $cacheFile = '../config/.licencia_cache.json';

    public static function checkEarly() {
        $url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
        $urlParts = explode('/', $url);
        $controller = strtolower($urlParts[0]);

        // Rutas excluidas de la verificación para evitar bucles infinitos
        $rutasExcluidas = ['license', 'licencia', 'licencia-web', 'public', 'css', 'js', 'uploads'];
        if (in_array($controller, $rutasExcluidas)) {
            return;
        }

        // 1. Verificar si la licencia fue marcada como suspendida/bloqueada remotamente
        $cache = self::getCache();
        if (($cache['remote_status'] ?? 'ok') === 'bloqueado') {
            self::responderOBloquear('SU LICENCIA HA SIDO SUSPENDIDA O PAUSADA');
            return;
        }

        // 2. Verificar la licencia local (JWT)
        $token = self::getToken();
        if (empty($token)) {
            self::responderOBloquear('SU LICENCIA REQUIERE ATENCIÓN');
            return;
        }

        $res = self::verifyToken($token);
        if (!$res['valid']) {
            self::responderOBloquear($res['error']);
            return;
        }

        // 3. Si es válida localmente, ejecutar el reporte asíncrono/revalidación en segundo plano
        self::reportarActivacion($token);
    }

    public static function isValid() {
        $cache = self::getCache();
        if (($cache['remote_status'] ?? 'ok') === 'bloqueado') {
            return false;
        }

        $token = self::getToken();
        if (empty($token)) return false;

        $res = self::verifyToken($token);
        return $res['valid'];
    }

    public static function verifyToken($token) {
        if (empty($token)) {
            return ['valid' => false, 'error' => 'SU LICENCIA REQUIERE ATENCIÓN'];
        }

        $parts = explode('.', trim($token));
        
        // ── CASO A: JWT Estándar HS256 (3 Partes) ─────────────────────────
        if (count($parts) === 3) {
            $secret = getenv('JWT_SECRET_LICENCIA') ?: ($_ENV['JWT_SECRET_LICENCIA'] ?? (getenv('JWT_SECRET') ?: ($_ENV['JWT_SECRET'] ?? 'Kj$8LmpP@qZ1xV#9RtW&3Nf*cYuTbE^2oSvA!')));
            
            $payloadJson = self::base64UrlDecode($parts[1]);
            if (!$payloadJson) {
                return ['valid' => false, 'error' => 'Payload Base64 del token JWT corrupto o no decodificable.'];
            }

            // Verificar firma HS256
            $defaultSecret  = 'Kj$8LmpP@qZ1xV#9RtW&3Nf*cYuTbE^2oSvA!';
            $signatureCheck = self::base64UrlEncode(hash_hmac('sha256', $parts[0] . '.' . $parts[1], $secret, true));
            if (!hash_equals($signatureCheck, $parts[2])) {
                // Probar también con el secret por defecto si hubo sustitución de caracteres $ por Docker Compose
                $signatureCheckDefault = self::base64UrlEncode(hash_hmac('sha256', $parts[0] . '.' . $parts[1], $defaultSecret, true));
                if (!hash_equals($signatureCheckDefault, $parts[2])) {
                    return ['valid' => false, 'error' => 'Firma del token JWT inválida. La clave secreta JWT_SECRET_LICENCIA no coincide con la del emisor.'];
                }
            }

            $data = json_decode($payloadJson, true);
            if (!$data) {
                return ['valid' => false, 'error' => 'El contenido Payload del token JWT no es un JSON válido.'];
            }

            // Comprobar Expiración (si exp está configurado)
            if (isset($data['exp']) && $data['exp'] > 0) {
                if (time() > (int)$data['exp']) {
                    return ['valid' => false, 'error' => 'SU LICENCIA HA EXPIRADO el ' . date('d/m/Y H:i', (int)$data['exp']) . '.'];
                }
            }

            return ['valid' => true, 'data' => $data, 'error' => null];
        }

        // ── CASO B: Token Legacy RSA (2 Partes) ───────────────────────────
        if (count($parts) === 2) {
            $payloadB64   = $parts[0];
            $signatureB64 = $parts[1];

            $payloadJson = self::base64UrlDecode($payloadB64);
            $signature   = self::base64UrlDecode($signatureB64);

            if (!$payloadJson || !$signature) {
                return ['valid' => false, 'error' => 'Payload o firma de licencia RSA corrupta.'];
            }

            $cleanPubKeyStr = str_replace(["\r\n", "\r"], "\n", trim(self::PUBLIC_KEY));
            $pubKey = @openssl_pkey_get_public($cleanPubKeyStr);
            if (!$pubKey) {
                return ['valid' => false, 'error' => 'No se pudo cargar la clave pública RSA de verificación.'];
            }

            $ok = @openssl_verify($payloadB64, $signature, $pubKey, OPENSSL_ALGO_SHA256);
            if ($ok !== 1) {
                return ['valid' => false, 'error' => 'Firma RSA de licencia inválida o manipulada.'];
            }

            $data = json_decode($payloadJson, true);
            if (!$data) {
                return ['valid' => false, 'error' => 'Payload JSON de licencia RSA no válido.'];
            }

            $fechaFin = isset($data['fecha_fin']) ? strtotime($data['fecha_fin']) : (isset($data['exp']) ? (int)$data['exp'] : null);
            if ($fechaFin && time() > $fechaFin) {
                return ['valid' => false, 'error' => 'SU LICENCIA HA EXPIRADO el ' . date('d/m/Y', $fechaFin) . '.'];
            }

            return ['valid' => true, 'data' => $data, 'error' => null];
        }

        return ['valid' => false, 'error' => 'Formato de token no reconocido. Debe ser un token JWT (3 partes separadas por puntos).'];
    }

    /**
     * Reporta de forma asíncrona/throttled la licencia al panel central.
     * Si el panel responde 401, 403 o 423, bloquea la licencia.
     * Si el panel está caído o sin conexión, opera en Modo Offline Resiliente.
     */
    public static function reportarActivacion($token) {
        $panelUrl   = rtrim(getenv('PANEL_LICENCIAS_URL') ?: ($_ENV['PANEL_LICENCIAS_URL'] ?? 'http://localhost:4000'), '/');
        $intervaloMs = intval(getenv('LICENCIA_INTERVALO_MS') ?: ($_ENV['LICENCIA_INTERVALO_MS'] ?? 3600000));

        $cache = self::getCache();
        $ahoraMs = (int)(microtime(true) * 1000);
        $ultimaVerif = (int)($cache['last_check_ms'] ?? 0);

        // Throttling: evitar llamadas repetidas dentro del intervalo si el estado es OK
        if (($ahoraMs - $ultimaVerif < $intervaloMs) && (($cache['remote_status'] ?? 'ok') === 'ok')) {
            return;
        }

        // Actualizar timestamp de intento
        $cache['last_check_ms'] = $ahoraMs;

        $body = json_encode([
            'token' => $token,
            'servidor' => gethostname()
        ]);

        $url = $panelUrl . '/api/activacion';

        // Petición HTTP remota con timeout corto de 5 segundos
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($body)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode === 0) {
            // Panel inaccesible -> Operar en Modo Offline Resiliente
            error_log('[Licencia] Panel remoto inaccesible (' . $curlError . '). Operando en Modo Offline Resiliente.');
            $cache['remote_status'] = 'ok';
        } else if (in_array($httpCode, [401, 403, 423])) {
            // El panel revoca explícitamente la licencia
            error_log('[Licencia] ATENCIÓN: El panel central ha desactivado o pausado esta licencia (HTTP ' . $httpCode . ').');
            $cache['remote_status'] = 'bloqueado';
        } else if ($httpCode === 200) {
            $cache['remote_status'] = 'ok';
        }

        self::saveCache($cache);
    }

    public static function forzarRevalidacion() {
        $cache = [
            'last_check_ms' => 0,
            'remote_status' => 'ok'
        ];
        self::saveCache($cache);
    }

    public static function getInfo() {
        $token = self::getToken();
        if (empty($token)) return null;

        $parts = explode('.', trim($token));
        if (count($parts) < 2) return null;

        $payloadB64 = (count($parts) === 3) ? $parts[1] : $parts[0];
        $payloadJson = self::base64UrlDecode($payloadB64);
        if (!$payloadJson) return null;

        $data = json_decode($payloadJson, true);
        if (!$data) return null;

        // Normalizar estructura de metadatos
        $data['ruc']        = $data['ruc'] ?? ($data['nit'] ?? 'N/A');
        $data['empresa_id'] = $data['empresa_id'] ?? ($data['id_empresa'] ?? ($data['cliente'] ?? 'N/A'));
        $data['cliente']    = $data['cliente'] ?? 'N/A';
        $data['tipo']       = $data['tipo'] ?? 'N/A';
        $data['emision']    = $data['iat'] ?? ($data['emision'] ?? null);

        if (isset($data['exp'])) {
            $data['fecha_fin'] = date('Y-m-d', (int)$data['exp']);
        }

        $cache = self::getCache();
        $data['last_check_ms'] = $cache['last_check_ms'] ?? 0;
        $data['remote_status'] = $cache['remote_status'] ?? 'ok';
        $data['panel_url']     = rtrim(getenv('PANEL_LICENCIAS_URL') ?: ($_ENV['PANEL_LICENCIAS_URL'] ?? 'http://localhost:4000'), '/');

        return $data;
    }

    public static function getToken() {
        // 1. Verificar si el token viene configurado como variable de entorno (Ideal para Dokploy / Docker)
        $envToken = getenv('LICENCIA_TOKEN') ?: ($_ENV['LICENCIA_TOKEN'] ?? null);
        if (!empty($envToken)) {
            return trim($envToken);
        }

        // 2. Verificar archivo local config/licencia.json
        if (file_exists(self::$jsonFile)) {
            $content = @file_get_contents(self::$jsonFile);
            $json = @json_decode($content, true);
            if (isset($json['token']) && !empty(trim($json['token']))) {
                return trim($json['token']);
            }
        }

        // 3. Verificar archivo legacy config/license.key
        if (file_exists(self::$keyFile)) {
            $token = trim(@file_get_contents(self::$keyFile));
            if (!empty($token)) {
                // Sincronizar hacia jsonFile
                self::saveToken($token);
                return $token;
            }
        }

        return '';
    }

    public static function saveToken($token) {
        $token = trim($token);
        
        // Guardar en config/licencia.json
        $jsonDir = dirname(self::$jsonFile);
        if (!file_exists($jsonDir)) { @mkdir($jsonDir, 0777, true); }
        @file_put_contents(self::$jsonFile, json_encode(['token' => $token], JSON_PRETTY_PRINT));

        // Guardar también en config/license.key para retrocompatibilidad
        @file_put_contents(self::$keyFile, $token);
    }

    private static function responderOBloquear($mensajeError) {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['error' => $mensajeError]);
            exit;
        } else {
            $_SESSION['error_licencia'] = $mensajeError;
            if (!headers_sent()) {
                header('Location: ' . BASE_URL . '/license?err=' . urlencode($mensajeError));
            } else {
                echo "<script>window.location.href='" . BASE_URL . "/license?err=" . urlencode($mensajeError) . "';</script>";
            }
            exit;
        }
    }

    private static function getCache() {
        if (file_exists(self::$cacheFile)) {
            $json = @json_decode(@file_get_contents(self::$cacheFile), true);
            if (is_array($json)) return $json;
        }
        return ['last_check_ms' => 0, 'remote_status' => 'ok'];
    }

    private static function saveCache($cache) {
        @file_put_contents(self::$cacheFile, json_encode($cache));
    }

    private static function base64UrlDecode($data) {
        $b64 = strtr($data, '-_', '+/');
        $remainder = strlen($b64) % 4;
        if ($remainder) {
            $b64 .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode($b64);
    }

    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
