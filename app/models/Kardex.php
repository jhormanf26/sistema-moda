<?php
require_once '../app/core/Database.php';

class Kardex {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Registrar un movimiento (Se puede llamar desde Venta o Producto)
    // Si pasamos la conexión ($conn) aprovechamos la transacción del padre
    public function registrar($variante_id, $tipo, $cantidad, $descripcion, $usuario_id, $conexion_externa = null) {
        
        $db = $conexion_externa ? $conexion_externa : $this->conn;

        $sql = "INSERT INTO kardex (variante_id, tipo, cantidad, descripcion, usuario_id, fecha) 
                VALUES (:vid, :tipo, :cant, :desc, :uid, NOW())";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':vid' => $variante_id,
            ':tipo' => $tipo,
            ':cant' => $cantidad,
            ':desc' => $descripcion,
            ':uid' => $usuario_id
        ]);
    }

    // Listar movimientos de un producto (Para verlo en el inventario)
    // Hacemos JOIN para saber el nombre de la variante
    public function obtenerHistorial($id_producto, $sucursal_id) {
        $sql = "SELECT k.*, v.talla, v.color, u.nombre as usuario 
                FROM kardex k
                JOIN producto_variantes v ON k.variante_id = v.id
                LEFT JOIN usuarios u ON k.usuario_id = u.id
                WHERE v.producto_id = :pid AND k.sucursal_id = :sid
                ORDER BY k.id DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':pid' => $id_producto, ':sid' => $sucursal_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}