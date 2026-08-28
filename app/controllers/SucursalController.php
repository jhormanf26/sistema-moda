<?php
require_once '../app/models/Sucursal.php';

class SucursalController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/index');
            exit;
        }
    }

    public function index() {
        if ($_SESSION['user_rol'] !== 'admin') {
            header('Location: ' . BASE_URL . '/home/index');
            exit;
        }
        $sucursalModel = new Sucursal();
        $sucursales = $sucursalModel->listar();
        require_once '../app/views/sucursal/index.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['user_rol'] === 'admin') {
            $sucursalModel = new Sucursal();
            $id = $_POST['id'] ?? '';
            $datos = [
                'nombre' => $_POST['nombre'],
                'direccion' => $_POST['direccion'],
                'telefono' => $_POST['telefono'],
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];

            if (empty($id)) {
                $sucursalModel->registrar($datos);
            } else {
                $sucursalModel->actualizar($id, $datos);
            }
            header('Location: ' . BASE_URL . '/sucursal/index?msg=success');
        }
    }

    // API JSON para el Modal de Edición
    public function obtener($id) {
        if ($_SESSION['user_rol'] !== 'admin') exit;
        $sucursalModel = new Sucursal();
        echo json_encode($sucursalModel->obtenerPorId($id));
    }
    
    // Cambiar la sucursal activa en la sesión
    public function cambiarActiva() {
        if ($_SESSION['user_rol'] !== 'admin') {
            header('Location: ' . BASE_URL . '/home/index');
            exit;
        }
        
        $id = $_POST['sucursal_id'] ?? $_GET['id'] ?? 1;
        
        $sucursalModel = new Sucursal();
        $sucursal = $sucursalModel->obtenerPorId($id);
        if ($sucursal && $sucursal['activo'] == 1) {
            $_SESSION['sucursal_id'] = $sucursal['id'];
            $_SESSION['sucursal_nombre'] = $sucursal['nombre'];
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/home/index';
        header("Location: $referer");
    }
}
