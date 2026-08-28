<?php
// app/controllers/PerfilController.php
require_once '../app/models/Usuario.php';

class PerfilController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/index');
            exit;
        }
    }

    // VISTA PERFIL (FIX: Añade la lógica para obtener el usuario actual)
    public function index() {
        $usuarioModel = new Usuario();
        // Obtenemos los datos del usuario logueado
        $usuario = $usuarioModel->obtenerPorId($_SESSION['user_id']); 

        require_once '../app/views/usuarios/perfil.php';
    }

    // PROCESAR CAMBIO DE CLAVE (se mantiene)
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $actual = $_POST['clave_actual'];
            $nueva = $_POST['clave_nueva'];
            $confirmar = $_POST['clave_confirmar'];

            if ($nueva !== $confirmar) {
                header('Location: ' . BASE_URL . '/perfil/index?error=mismatch');
                return;
            }

            $usuarioModel = new Usuario();
            if ($usuarioModel->cambiarClave($_SESSION['user_id'], $actual, $nueva)) {
                header('Location: ' . BASE_URL . '/perfil/index?msg=success');
            } else {
                header('Location: ' . BASE_URL . '/perfil/index?error=wrong_pass');
            }
        }
    }
}