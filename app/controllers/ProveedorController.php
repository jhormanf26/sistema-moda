<?php
require_once '../app/models/Proveedor.php';

class ProveedorController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Seguridad: Solo Admin
        if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 'admin') {
            header('Location: ' . BASE_URL . '/ventas');
            exit;
        }
    }

    public function index() {
        $proveedorModel = new Proveedor();
        $proveedores = $proveedorModel->listar();
        require_once '../app/views/proveedores/index.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $proveedorModel = new Proveedor();
            $id = $_POST['id'] ?? '';
            $datos = [
                'ruc' => $_POST['ruc'],
                'razon_social' => $_POST['razon_social'],
                'telefono' => $_POST['telefono'],
                'correo' => $_POST['correo'],
                'direccion' => $_POST['direccion']
            ];

            if (empty($id)) {
                $proveedorModel->registrar($datos);
            } else {
                $proveedorModel->actualizar($id, $datos);
            }
            header('Location: ' . BASE_URL . '/proveedor/index?msg=success');
        }
    }

    // API JSON para editar
    public function obtener($id) {
        $proveedorModel = new Proveedor();
        echo json_encode($proveedorModel->obtenerPorId($id));
    }
}