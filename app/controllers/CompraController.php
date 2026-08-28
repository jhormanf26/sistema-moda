<?php
// app/controllers/CompraController.php
require_once '../app/models/Compra.php';
require_once '../app/models/Producto.php';
require_once '../app/models/Proveedor.php';

class CompraController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
            header('Location: ' . BASE_URL . '/auth/index');
            exit;
        }
    }

    public function crear() {
        $proveedorModel = new Proveedor();
        $proveedores = $proveedorModel->listar();
        require_once '../app/views/compras/crear.php';
    }

    // Buscador para el autocompletado de compras
    public function buscar($termino) {
        $db = new Database();
        $conn = $db->getConnection();
        $termino = "%" . $termino . "%";
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
        
        $sql = "SELECT 
                    p.id, p.nombre, p.precio_compra,
                    v.id as variante_id, v.talla, v.color, IFNULL(inv.stock_actual, 0) as stock_actual
                FROM productos p
                JOIN producto_variantes v ON p.id = v.producto_id
                LEFT JOIN inventario_sucursales inv ON v.id = inv.variante_id AND inv.sucursal_id = :sid
                WHERE p.activo = 1 
                AND (p.nombre LIKE :t1 OR p.codigo_barras_base LIKE :t2 OR v.codigo_barras_variante LIKE :t3)
                LIMIT 15";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':t1' => $termino, ':t2' => $termino, ':t3' => $termino, ':sid' => $sucursal_id]);
        
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function guardar() {
        header('Content-Type: application/json');
        
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        if (!$data) {
            echo json_encode(['status' => false, 'message' => 'Datos inválidos o vacíos']);
            return;
        }

        // Asignar el usuario logueado
        $data['usuario_id'] = $_SESSION['user_id'];
        $data['sucursal_id'] = $_SESSION['sucursal_id'] ?? 1;

        try {
            $compraModel = new Compra();
            $resultado = $compraModel->registrar($data);
            echo json_encode($resultado);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => 'Excepción: ' . $e->getMessage()]);
        }
    }
}