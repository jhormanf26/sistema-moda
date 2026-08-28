<?php
require_once '../app/core/Database.php';

class Reporte {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // 1. Total vendido HOY
    public function ventasHoy($sucursal_id) {
        $sql = "SELECT SUM(total) as total, COUNT(*) as transacciones FROM ventas WHERE DATE(fecha) = CURDATE() AND sucursal_id = :sid AND estado = 'completada'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':sid' => $sucursal_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. Ventas de los últimos 7 días (Para el gráfico de líneas)
    public function ventasUltimos7Dias($sucursal_id) {
        $sql = "SELECT DATE(fecha) as fecha, SUM(total) as total 
                FROM ventas 
                WHERE fecha >= DATE(NOW()) - INTERVAL 7 DAY AND sucursal_id = :sid AND estado = 'completada'
                GROUP BY DATE(fecha)
                ORDER BY fecha ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':sid' => $sucursal_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Top 5 Productos más vendidos (Para el gráfico de torta/barras)
    public function productosMasVendidos($sucursal_id) {
        $sql = "SELECT p.nombre, SUM(d.cantidad) as cantidad
                FROM venta_detalles d
                JOIN ventas v2 ON d.venta_id = v2.id
                JOIN producto_variantes v ON d.variante_id = v.id
                JOIN productos p ON v.producto_id = p.id
                WHERE v2.sucursal_id = :sid AND v2.estado = 'completada'
                GROUP BY p.id
                ORDER BY cantidad DESC
                LIMIT 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':sid' => $sucursal_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Total de productos activos en el sistema
    public function totalProductosActivos() {
        $sql = "SELECT COUNT(*) as total FROM productos WHERE activo = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // 5. Alertas de bajo stock (Variantes con stock <= 5)
    public function alertasBajoStock($sucursal_id) {
        $sql = "SELECT COUNT(*) as alertas FROM inventario_sucursales WHERE stock_actual <= 5 AND sucursal_id = :sid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':sid' => $sucursal_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['alertas'] ?? 0;
    }

    // 6. Total vendido en el mes actual
    public function ventasDelMes($sucursal_id) {
        $sql = "SELECT SUM(total) as total, COUNT(*) as transacciones 
                FROM ventas 
                WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE()) AND sucursal_id = :sid AND estado = 'completada'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':sid' => $sucursal_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}