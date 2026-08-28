<?php
// app/controllers/MovimientoController.php
require_once '../app/models/Producto.php';
require_once '../app/models/Kardex.php';
require_once '../app/core/Database.php';

class MovimientoController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/index');
            exit;
        }
    }

    // VISTA PRINCIPAL
    public function index() {
        // No listamos movimientos aquí, solo el formulario
        require_once '../app/views/movimientos/index.php';
    }

    // PROCESAR AJUSTE DE STOCK Y REGISTRO EN KARDEX
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $id_variante = $_POST['id_variante'];
            $tipo_movimiento = $_POST['tipo']; // 'entrada' o 'salida'
            $cantidad = intval($_POST['cantidad']);
            $motivo = $_POST['motivo'];
            $usuario_id = $_SESSION['user_id'];

            if ($cantidad <= 0 || empty($id_variante)) {
                header('Location: ' . BASE_URL . '/movimiento/index?error=' . urlencode("La cantidad o el producto es inválido."));
                return;
            }

            $db = new Database();
            $conn = $db->getConnection();

            try {
                $conn->beginTransaction();

                // 1. Determinar y ejecutar el SQL para actualizar stock
                if ($tipo_movimiento == 'entrada') {
                    $sqlStock = "UPDATE producto_variantes SET stock_actual = stock_actual + :cant WHERE id = :id";
                } else {
                    $sqlStock = "UPDATE producto_variantes SET stock_actual = stock_actual - :cant WHERE id = :id AND stock_actual >= :cant";
                }

                $stmt = $conn->prepare($sqlStock);
                $stmt->execute([':cant' => $cantidad, ':id' => $id_variante]);

                if ($tipo_movimiento == 'salida' && $stmt->rowCount() == 0) {
                    throw new Exception("Stock actual insuficiente para registrar la merma.");
                }

                // 2. REGISTRAR EN KARDEX
                $sqlKardex = "INSERT INTO kardex (variante_id, tipo, cantidad, descripcion, usuario_id, fecha) 
                              VALUES (:vid, :tipo, :cant, :desc, :uid, NOW())";
                $stmtK = $conn->prepare($sqlKardex);
                $stmtK->execute([
                    ':vid' => $id_variante,
                    ':tipo' => $tipo_movimiento,
                    ':cant' => $cantidad,
                    ':desc' => "Ajuste Manual: " . $motivo,
                    ':uid' => $usuario_id
                ]);

                $conn->commit();
                header('Location: ' . BASE_URL . '/movimiento/index?msg=success');

            } catch (Exception $e) {
                $conn->rollBack();
                header('Location: ' . BASE_URL . '/movimiento/index?error=' . urlencode($e->getMessage()));
            }
        }
    }
}