<?php
// app/core/License.php

class License {

    const PUBLIC_KEY = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAt9C7YR2fqwZM7fSmwc/p
+rKBDjixp176WFDcLMU4jfs82QwhXuvbJiB9T2MMzph46GW1tyznymIValoYyXpE
/f7o4d1uv3oYixKZ40JYAlUpygkLeVdkfP2DS2hwCZSx5U3c01TdcDXHBMjkRO/k
pM88kgAPmq3xeRqU1H20L1Kg5zzrnp3ULxLC1jj+x9PPCljXTqjN6plST1YFi2DZ
zQGJw4ImDtq27wICMaaep8BYbstipCrgzEWZmGFJtWcGohFJqBB3Ly17dCyruuDd
pS4PqA/yJ3eR5r7RBJ5tZv8LvEZ/EBJSFSIGegm+WKtVQloLJtoy+V9aMUX1Vl4c
6wIDAQAB
-----END PUBLIC KEY-----
EOD;

    private static $licenseFile = '../config/license.key';

    public static function checkEarly() {
        // Obviar rutas que pertenecen al controlador de Licencia
        $url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
        $urlParts = explode('/', $url);
        $controller = strtolower($urlParts[0]);

        if ($controller === 'license') {
            return; // Permitir el paso para activar
        }

        // Si no es válida, redirigir
        if (!self::isValid()) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                // Es AJAX, responder con JSON
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Licencia expirada o inválida.']);
                exit;
            } else {
                header('Location: ' . BASE_URL . '/license');
                exit;
            }
        }
    }

    public static function isValid() {
        if (!file_exists(self::$licenseFile)) return false;

        $token = trim(file_get_contents(self::$licenseFile));
        if (empty($token)) return false;

        $parts = explode('.', $token);
        if (count($parts) !== 2) return false;

        $payloadB64 = $parts[0];
        $signatureB64 = $parts[1];

        $payloadJson = self::base64UrlDecode($payloadB64);
        $signature = self::base64UrlDecode($signatureB64);

        if (!$payloadJson || !$signature) return false;

        // Verificar la firma RSA
        $ok = openssl_verify($payloadB64, $signature, self::PUBLIC_KEY, OPENSSL_ALGO_SHA256);
        if ($ok !== 1) {
            return false; // Firma inválida o alterada
        }

        // Parsear y verificar la fecha
        $data = json_decode($payloadJson, true);
        if (!$data || !isset($data['fecha_fin'])) return false;

        $fechaFin = strtotime($data['fecha_fin']);
        $hoy = time();

        if ($hoy > $fechaFin) {
            return false; // Licencia Expirada
        }

        return true;
    }

    public static function getInfo() {
        if (!file_exists(self::$licenseFile)) return null;
        $token = trim(file_get_contents(self::$licenseFile));
        if (empty($token)) return null;
        $parts = explode('.', $token);
        if (count($parts) !== 2) return null;
        return json_decode(self::base64UrlDecode($parts[0]), true);
    }

    public static function saveToken($token) {
        return file_put_contents(self::$licenseFile, trim($token));
    }

    private static function base64UrlDecode($data) {
        $b64 = strtr($data, '-_', '+/');
        return base64_decode($b64);
    }
}
