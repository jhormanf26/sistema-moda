<?php
// app/controllers/LicenseController.php
require_once '../app/core/License.php';

class LicenseController {

    // ── Pantalla de bloqueo (sin sesión) ────────────────────────────────────
    public function index() {
        if (License::isValid()) {
            header('Location: ' . BASE_URL);
            exit;
        }
        $error = isset($_GET['err']) ? 'Firma inválida o licencia expirada.' : '';
        require_once '../app/views/license/index.php';
    }

    // ── Panel de estado de licencia (dentro del sistema autenticado) ─────────
    public function panel() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/index');
            exit;
        }

        $licInfo    = License::getInfo();
        $licValida  = License::isValid();
        $success    = isset($_GET['ok'])  ? true : false;
        $error      = isset($_GET['err']) ? 'Token inválido o ya expirado. Verifique e intente nuevamente.' : '';

        require_once '../app/views/license/panel.php';
    }

    // ── Activar / renovar licencia (desde bloqueo O desde panel interno) ─────
    public function activar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/license');
            exit;
        }

        $token    = trim($_POST['token'] ?? '');
        $from     = $_POST['from'] ?? 'block'; // 'block' | 'panel'

        if (empty($token)) {
            $redir = ($from === 'panel') ? BASE_URL . '/license/panel?err=1' : BASE_URL . '/license?err=1';
            header('Location: ' . $redir);
            exit;
        }

        $oldToken = file_exists('../config/license.key') ? file_get_contents('../config/license.key') : '';
        License::saveToken($token);

        if (License::isValid()) {
            $redir = ($from === 'panel') ? BASE_URL . '/license/panel?ok=1' : BASE_URL;
            header('Location: ' . $redir);
        } else {
            // Revertir al token anterior
            if ($oldToken) { License::saveToken($oldToken); }
            else { @unlink('../config/license.key'); }

            $redir = ($from === 'panel') ? BASE_URL . '/license/panel?err=1' : BASE_URL . '/license?err=1';
            header('Location: ' . $redir);
        }
        exit;
    }
}

