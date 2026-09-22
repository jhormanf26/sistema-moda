<?php
// app/controllers/ComboController.php

require_once '../app/models/Combo.php';
require_once '../app/models/Producto.php';
require_once '../app/models/Empresa.php';

class ComboController {
    private $comboModel;
    private $productoModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/index');
            exit;
        }
        $this->comboModel = new Combo();
        $this->productoModel = new Producto();
    }

    public function index() {
        $combos = $this->comboModel->listar();
        $productos = $this->productoModel->obtenerProductosTienda();
        $empresaModel = new Empresa();
        $empresa = $empresaModel->obtener();

        require_once '../app/views/combos/index.php';
    }

    public function guardar() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
            exit;
        }

        $id          = !empty($_POST['id']) ? intval($_POST['id']) : null;
        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio      = floatval($_POST['precio'] ?? 0);
        $itemsRaw    = $_POST['items'] ?? [];

        if (empty($nombre) || $precio <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'El nombre y el precio del combo son obligatorios.']);
            exit;
        }

        // Procesar array de ítems (productos y cantidades)
        $items = [];
        if (is_array($itemsRaw)) {
            foreach ($itemsRaw as $it) {
                if (!empty($it['producto_id']) && intval($it['cantidad']) > 0) {
                    $items[] = [
                        'producto_id' => intval($it['producto_id']),
                        'cantidad'    => intval($it['cantidad'])
                    ];
                }
            }
        }

        if (count($items) === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Debe agregar al menos un producto al combo.']);
            exit;
        }

        // Procesar imagen si se subió
        $rutaImagen = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (in_array($ext, $permitidas)) {
                $dirSubida = '../public/uploads/combos/';
                if (!file_exists($dirSubida)) {
                    mkdir($dirSubida, 0777, true);
                }
                $nombreArchivo = 'combo_' . time() . '_' . uniqid() . '.' . $ext;
                $destino = $dirSubida . $nombreArchivo;
                
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                    $rutaImagen = 'uploads/combos/' . $nombreArchivo;
                }
            }
        }

        try {
            $datos = [
                'nombre'      => $nombre,
                'descripcion' => $descripcion,
                'precio'      => $precio,
                'imagen'      => $rutaImagen
            ];

            if ($id) {
                $res = $this->comboModel->actualizar($id, $datos, $items);
                $msg = 'Combo actualizado exitosamente.';
            } else {
                $res = $this->comboModel->registrar($datos, $items);
                $msg = 'Combo creado exitosamente.';
            }

            if ($res) {
                echo json_encode(['status' => 'success', 'message' => $msg]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo guardar el combo.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    public function cambiarEstado() {
        header('Content-Type: application/json');
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        $estado = isset($_POST['estado']) ? intval($_POST['estado']) : 0;

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
            exit;
        }

        $res = $this->comboModel->cambiarEstado($id, $estado);
        if ($res) {
            echo json_encode(['status' => 'success', 'message' => 'Estado del combo actualizado.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al cambiar estado.']);
        }
        exit;
    }

    public function eliminar() {
        header('Content-Type: application/json');
        $id = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : null);

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
            exit;
        }

        $combo = $this->comboModel->obtenerPorId($id);
        if ($combo && !empty($combo['imagen'])) {
            $rutaFisica = '../public/' . $combo['imagen'];
            if (file_exists($rutaFisica)) {
                @unlink($rutaFisica);
            }
        }

        $res = $this->comboModel->eliminar($id);
        if ($res) {
            echo json_encode(['status' => 'success', 'message' => 'Combo eliminado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar el combo.']);
        }
        exit;
    }
}
