<?php
require_once '../app/models/Usuario.php';

class UsuarioController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // SEGURIDAD: Solo Admin puede gestionar usuarios
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
            header('Location: ' . BASE_URL . '/ventas'); // O a donde quieras enviarlos
            exit;
        }
    }

    public function index() {
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->listar();
        
        require_once '../app/models/Sucursal.php';
        $sucursalModel = new Sucursal();
        $sucursales_activas = $sucursalModel->listarActivas();
        
        require_once '../app/views/usuarios/index.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuarioModel = new Usuario();
            
            // Si viene ID, es Editar. Si no, es Nuevo.
            $id = $_POST['id'] ?? '';
            $nombre = $_POST['nombre'];
            $correo = $_POST['correo'];
            $rol = $_POST['rol'];
            $password = $_POST['password'];
            $sucursal_id = $_POST['sucursal_id'];

            if(empty($id)) {
                // Crear
                if(!empty($password)) {
                    $usuarioModel->registrar($nombre, $correo, $password, $rol, $sucursal_id);
                }
            } else {
                // Editar (Password opcional)
                $usuarioModel->actualizar($id, $nombre, $correo, $rol, $sucursal_id, $password);
            }
            
            header('Location: ' . BASE_URL . '/usuario/index?msg=success');
        }
    }

    public function cambiarEstado($id, $estadoActual) {
        // Evitar que el admin se bloquee a sí mismo
        if($id == $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/usuario/index?error=self');
            return;
        }

        $usuarioModel = new Usuario();
        $nuevoEstado = ($estadoActual == 1) ? 0 : 1;
        $usuarioModel->cambiarEstado($id, $nuevoEstado);
        
        header('Location: ' . BASE_URL . '/usuario/index?msg=updated');
    }
    
    // API JSON para cargar datos en el Modal de Edición
    public function obtener($id) {
        $usuarioModel = new Usuario();
        $data = $usuarioModel->obtenerPorId($id);
        echo json_encode($data);
    }
}