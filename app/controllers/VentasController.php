<?php
// app/controllers/VentasController.php

class VentasController {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // 1. Seguridad Login
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/index');
            exit;
        }

        // 2. Seguridad Caja Abierta
        require_once '../app/models/Caja.php';
        $cajaModel = new Caja();
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
        $caja = $cajaModel->obtenerCajaAbierta($_SESSION['user_id'], $sucursal_id);

        if (!$caja) {
            header('Location: ' . BASE_URL . '/caja/index');
            exit;
        }
    }

    public function index() {
        require_once '../app/views/ventas/tpv.php';
    }

    // FUNCIÓN BUSCAR PRODUCTOS
    public function buscar($termino) {
        if(empty($termino)) { echo json_encode([]); return; }
        
        $db = new Database();
        $conn = $db->getConnection();
        
        $termino_limpio = '%' . trim($termino) . '%';
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;

        $sql = "SELECT p.nombre, p.precio_venta, p.imagen, p.activo,
                       v.id as variante_id, v.talla, v.color, inv.stock_actual, v.codigo_barras_variante
                FROM productos p 
                JOIN producto_variantes v ON p.id = v.producto_id
                JOIN inventario_sucursales inv ON v.id = inv.variante_id AND inv.sucursal_id = :sucursal
                WHERE (
                    p.nombre LIKE :termino_nombre 
                    OR v.codigo_barras_variante = :codigo 
                    OR p.codigo_barras_base = :codigo
                )
                AND p.activo = 1"; 
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':termino_nombre' => $termino_limpio, 
            ':codigo' => $termino,
            ':sucursal' => $sucursal_id
        ]);
        
        header('Content-Type: application/json');
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // FUNCIÓN BUSCAR POR CATEGORÍA
    public function buscarPorCategoria($categoria_id) {
        $db = new Database();
        $conn = $db->getConnection();
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;

        $sql = "SELECT p.nombre, p.precio_venta, p.imagen, p.activo,
                       v.id as variante_id, v.talla, v.color, inv.stock_actual, v.codigo_barras_variante
                FROM productos p 
                JOIN producto_variantes v ON p.id = v.producto_id
                JOIN inventario_sucursales inv ON v.id = inv.variante_id AND inv.sucursal_id = :sucursal
                WHERE p.activo = 1 ";
                
        if ($categoria_id !== 'todas') {
            $sql .= " AND p.categoria_id = :categoria";
        }
        
        $stmt = $conn->prepare($sql);
        
        if ($categoria_id !== 'todas') {
            $stmt->execute([':sucursal' => $sucursal_id, ':categoria' => $categoria_id]);
        } else {
            $stmt->execute([':sucursal' => $sucursal_id]);
        }
        
        header('Content-Type: application/json');
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function guardar() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['carrito']) && count($data['carrito']) > 0) {
            require_once '../app/models/Venta.php';
            $ventaModel = new Venta();
            
            $usuario_id = $_SESSION['user_id']; 
            $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
            $total = $data['total'];
            $cliente_id = isset($data['cliente_id']) ? $data['cliente_id'] : 1;
            $forma_pago = isset($data['forma_pago']) ? $data['forma_pago'] : 'efectivo';

            $resultado = $ventaModel->registrarVenta($usuario_id, $data['carrito'], $total, $cliente_id, $sucursal_id, $forma_pago);
            echo json_encode($resultado);
        } else {
            echo json_encode(['status' => false, 'message' => 'Carrito vacío']);
        }
    }

    public function imprimir($id) {
        require_once '../app/models/Venta.php';
        require_once '../app/models/Empresa.php';
        $ventaModel = new Venta();
        $empresaModel = new Empresa(); 
        $venta = $ventaModel->obtenerVenta($id);
        $detalles = $ventaModel->obtenerDetalles($id);
        $empresa = $empresaModel->obtener(); 
        if (!$venta) { echo "Venta no encontrada."; return; }
        require_once '../app/views/ventas/ticket.php';
    }

    public function historial() {
        require_once '../app/models/Venta.php';
        $ventaModel = new Venta();
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
        
        // Recibir filtros
        $fecha_inicio = $_GET['fecha_inicio'] ?? null;
        $fecha_fin = $_GET['fecha_fin'] ?? null;
        $busqueda = $_GET['busqueda'] ?? null;

        $ventas = $ventaModel->listarHistorial($sucursal_id, $fecha_inicio, $fecha_fin, $busqueda);
        require_once '../app/views/ventas/historial.php';
    }

    public function anular($id) {
        if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 'admin') {
            header('Location: ' . BASE_URL . '/ventas/historial?error=permission');
            return;
        }
        $motivo = isset($_GET['motivo']) ? urldecode($_GET['motivo']) : 'Sin motivo';
        $id_admin = $_SESSION['user_id'];
        require_once '../app/models/Venta.php';
        $ventaModel = new Venta();
        if ($ventaModel->anular($id, $motivo, $id_admin)) {
            header('Location: ' . BASE_URL . '/ventas/historial?msg=canceled');
        } else {
            header('Location: ' . BASE_URL . '/ventas/historial?error=true');
        }
    }

    public function exportar() {
        if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 'admin') { header('Location: ' . BASE_URL . '/ventas/historial'); return; }
        ob_clean();
        require_once '../app/models/Venta.php';
        $ventaModel = new Venta();
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
        
        // Recibir filtros
        $fecha_inicio = $_GET['fecha_inicio'] ?? null;
        $fecha_fin = $_GET['fecha_fin'] ?? null;
        $busqueda = $_GET['busqueda'] ?? null;

        $ventas = $ventaModel->listarHistorial($sucursal_id, $fecha_inicio, $fecha_fin, $busqueda);
        
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=ventas_" . date('Y-m-d') . ".xls");
        header("Pragma: no-cache"); header("Expires: 0");
        echo "\xEF\xBB\xBF"; 
        echo "<table border='1'>";
        echo "<tr style='background-color: #e3f2fd; font-weight: bold;'><th>Ticket</th><th>Fecha</th><th>Cliente</th><th>Vendedor</th><th>Total</th><th>Estado</th><th>Anulado Por</th><th>Motivo</th></tr>";
        foreach ($ventas as $v) {
            $total = number_format($v['total'], 2);
            $cliente = mb_convert_encoding($v['cliente_nombre'] ?? 'Público General', 'ISO-8859-1', 'UTF-8');
            $vendedor = mb_convert_encoding($v['vendedor'], 'ISO-8859-1', 'UTF-8');
            $bg = ($v['estado'] == 'anulada') ? '#ffebee' : '#ffffff';
            $quien = ($v['estado'] == 'anulada') ? mb_convert_encoding($v['quien_anulo'], 'ISO-8859-1', 'UTF-8') : '-';
            $motivo = ($v['estado'] == 'anulada') ? mb_convert_encoding($v['motivo_anulacion'], 'ISO-8859-1', 'UTF-8') : '-';
            echo "<tr style='background-color: {$bg};'>
                    <td>#".str_pad($v['id'], 6, '0', STR_PAD_LEFT)."</td><td>{$v['fecha']}</td><td>{$cliente}</td><td>{$vendedor}</td>
                    <td>{$total}</td><td>{$v['estado']}</td><td>{$quien}</td><td>{$motivo}</td></tr>";
        }
        echo "</table>";
        exit;
    }
}