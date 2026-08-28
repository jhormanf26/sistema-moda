<?php
// app/controllers/CategoriaController.php
require_once '../app/models/Categoria.php';

class CategoriaController {
    private $categoriaModel;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
            header("Location: " . BASE_URL . "/auth/index");
            exit();
        }
        $this->categoriaModel = new Categoria();
    }

    public function index() {
        $categorias = $this->categoriaModel->listarTodas();
        require_once '../app/views/categorias/index.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion']
            ];

            if ($this->categoriaModel->registrar($datos)) {
                header("Location: " . BASE_URL . "/categoria/index?success=creado");
            } else {
                header("Location: " . BASE_URL . "/categoria/index?error=creado");
            }
        }
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $datos = [
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion']
            ];

            if ($this->categoriaModel->actualizar($id, $datos)) {
                header("Location: " . BASE_URL . "/categoria/index?success=actualizado");
            } else {
                header("Location: " . BASE_URL . "/categoria/index?error=actualizado");
            }
        }
    }

    public function desactivar($id) {
        if ($this->categoriaModel->cambiarEstado($id, 0)) {
            header("Location: " . BASE_URL . "/categoria/index?success=desactivado");
        } else {
            header("Location: " . BASE_URL . "/categoria/index?error=estado");
        }
    }

    public function activar($id) {
        if ($this->categoriaModel->cambiarEstado($id, 1)) {
            header("Location: " . BASE_URL . "/categoria/index?success=activado");
        } else {
            header("Location: " . BASE_URL . "/categoria/index?error=estado");
        }
    }

    // Método para API (Fetch desde TPV si fuese necesario)
    public function listarJSON() {
        header('Content-Type: application/json');
        echo json_encode($this->categoriaModel->listarActivas());
    }
}
