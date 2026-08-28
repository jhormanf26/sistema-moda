<?php
// app/controllers/AuthController.php
require_once '../app/models/Usuario.php';

class AuthController {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // CAMBIO: Si ya está logueado, ir al Panel Principal (Dashboard)
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/home/index');
            return;
        }
        require_once '../app/views/auth/login.php';
    }

    public function acceder() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $correo = $_POST['correo'];
            $password = $_POST['password'];

            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->login($correo, $password);

            if ($usuario) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_nombre'] = $usuario['nombre'];
                $_SESSION['user_rol'] = $usuario['rol'];
                $_SESSION['sucursal_id'] = $usuario['sucursal_id'];
                $_SESSION['sucursal_nombre'] = $usuario['sucursal_nombre'];

                // CAMBIO: Redirigir al Panel Principal tras loguearse
                header('Location: ' . BASE_URL . '/home/index');
            } else {
                header('Location: ' . BASE_URL . '/auth/index?error=true');
            }
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header('Location: ' . BASE_URL . '/auth/index');
    }

    public function setup() {
        $usuarioModel = new Usuario();
        try {
            $usuarioModel->crear('Admin Principal', 'admin@tienda.com', '123456');
            echo "Usuario creado. <a href='" . BASE_URL . "/auth/index'>Ir al Login</a>";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}