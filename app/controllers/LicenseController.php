<?php
// app/controllers/LicenseController.php
require_once '../app/core/License.php';

class LicenseController {

    // ── Pantalla de bloqueo (sin sesión o licencia bloqueada/expirada) ──────
    public function index() {
        if (License::isValid()) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $error = $_SESSION['error_licencia'] ?? ($_GET['err'] ?? '');
        unset($_SESSION['error_licencia']);

        if (empty($error)) {
            $error = 'SU LICENCIA REQUIERE ATENCIÓN';
        }

        require_once '../app/views/license/index.php';
    }

    // ── Revalidar licencia actual contra el panel ───────────────────────────
    public function revalidar() {
        License::forzarRevalidacion();
        header('Location: ' . BASE_URL);
        exit;
    }

    // ── Panel de estado de licencia (dentro del sistema autenticado) ─────────
    public function panel() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/index');
            exit;
        }

        $licInfo   = License::getInfo();
        $licValida = License::isValid();
        $success   = isset($_GET['ok'])  ? true : false;
        $error     = isset($_GET['err']) ? 'Token inválido o ya expirado. Verifique e intente nuevamente.' : '';

        require_once '../app/views/license/panel.php';
    }

    // ── Activar / renovar licencia (desde bloqueo O desde panel interno) ─────
    public function activar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/license');
            exit;
        }

        $token = trim($_POST['token'] ?? '');
        $from  = $_POST['from'] ?? 'block'; // 'block' | 'panel'

        if (empty($token)) {
            $redir = ($from === 'panel') ? BASE_URL . '/license/panel?err=1' : BASE_URL . '/license?err=' . urlencode('El token de activación está vacío.');
            header('Location: ' . $redir);
            exit;
        }

        $tokenPrevio = License::getToken();
        License::saveToken($token);
        License::forzarRevalidacion();

        $verif = License::verifyToken($token);

        if ($verif['valid']) {
            $redir = ($from === 'panel') ? BASE_URL . '/license/panel?ok=1' : BASE_URL;
            header('Location: ' . $redir);
        } else {
            // Restaurar token previo
            if (!empty($tokenPrevio)) {
                License::saveToken($tokenPrevio);
            }

            $errMsg = $verif['error'] ?? 'Token inválido o expirado.';
            $redir = ($from === 'panel') ? BASE_URL . '/license/panel?err=1' : BASE_URL . '/license?err=' . urlencode($errMsg);
            header('Location: ' . $redir);
        }
        exit;
    }
}
