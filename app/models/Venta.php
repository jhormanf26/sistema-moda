<?php
// app/models/Venta.php
require_once '../app/core/Database.php';

class Venta {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // 1. REGISTRAR VENTA (Con Kardex)
    public function registrarVenta($usuario_id, $carrito, $total, $cliente_id = 1, $sucursal_id = 1, $forma_pago = 'efectivo') {
        try {
            $this->conn->beginTransaction();

            // A. Insertar Venta
            $sql = "INSERT INTO ventas (usuario_id, cliente_id, total, fecha, estado, sucursal_id, forma_pago) 
                    VALUES (:user, :cli, :total, NOW(), 'completada', :suc, :pago)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':user' => $usuario_id, 
                ':cli' => $cliente_id, 
                ':total' => $total,
                ':suc' => $sucursal_id,
                ':pago' => $forma_pago
            ]);
            $venta_id = $this->conn->lastInsertId();

            // Preparar consultas
            $sqlDetalle = "INSERT INTO venta_detalles (venta_id, variante_id, cantidad, precio_unitario, subtotal) VALUES (:vid, :var_id, :cant, :precio, :sub)";
            $stmtDetalle = $this->conn->prepare($sqlDetalle);

            $sqlStock = "UPDATE inventario_sucursales SET stock_actual = stock_actual - :cant WHERE variante_id = :id AND sucursal_id = :sid AND stock_actual >= :cant";
            $stmtStock = $this->conn->prepare($sqlStock);

            // Consulta para el Kardex (SALIDA)
            $sqlKardex = "INSERT INTO kardex (variante_id, tipo, cantidad, descripcion, usuario_id, fecha, sucursal_id) 
                          VALUES (:vid, 'salida', :cant, :desc, :uid, NOW(), :sid)";
            $stmtKardex = $this->conn->prepare($sqlKardex);

            // B. Procesar Items
            foreach ($carrito as $item) {
                // 1. Guardar Detalle
                $stmtDetalle->execute([
                    ':vid' => $venta_id,
                    ':var_id' => $item['variante_id'],
                    ':cant' => $item['cantidad'],
                    ':precio' => $item['precio'],
                    ':sub' => $item['precio'] * $item['cantidad']
                ]);

                // 2. Restar Stock
                $stmtStock->execute([
                    ':cant' => $item['cantidad'],
                    ':id' => $item['variante_id'],
                    ':sid' => $sucursal_id
                ]);

                if ($stmtStock->rowCount() == 0) {
                    throw new Exception("Stock insuficiente para: " . $item['nombre']);
                }

                // 3. Registrar en KARDEX (Salida)
                $stmtKardex->execute([
                    ':vid' => $item['variante_id'],
                    ':cant' => $item['cantidad'],
                    ':desc' => "Venta Ticket #" . str_pad($venta_id, 6, '0', STR_PAD_LEFT),
                    ':uid' => $usuario_id,
                    ':sid' => $sucursal_id
                ]);
            }

            $this->conn->commit();
            return ['status' => true, 'id_venta' => $venta_id];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    // 2. OBTENER VENTA (Ticket)
    public function obtenerVenta($id) {
        $sql = "SELECT v.*, 
                       u.nombre as vendedor, 
                       c.nombre as cliente_nombre, 
                       c.documento as cliente_doc 
                FROM ventas v 
                JOIN usuarios u ON v.usuario_id = u.id 
                JOIN clientes c ON v.cliente_id = c.id
                WHERE v.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. OBTENER DETALLES
    public function obtenerDetalles($venta_id) {
        $sql = "SELECT d.*, p.nombre, v.talla, v.color 
                FROM venta_detalles d
                JOIN producto_variantes v ON d.variante_id = v.id
                JOIN productos p ON v.producto_id = p.id
                WHERE d.venta_id = :vid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':vid' => $venta_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. LISTAR HISTORIAL CON FILTROS
    public function listarHistorial($sucursal_id, $fecha_inicio = null, $fecha_fin = null, $busqueda = null) {
        $sql = "SELECT v.*, 
                       u.nombre as vendedor, 
                       ua.nombre as quien_anulo,
                       c.nombre as cliente_nombre
                FROM ventas v 
                JOIN usuarios u ON v.usuario_id = u.id 
                JOIN clientes c ON v.cliente_id = c.id
                LEFT JOIN usuarios ua ON v.usuario_anulacion_id = ua.id
                WHERE v.sucursal_id = :sid";
        
        $params = [':sid' => $sucursal_id];

        if ($fecha_inicio && $fecha_fin) {
            $sql .= " AND DATE(v.fecha) BETWEEN :f_inicio AND :f_fin";
            $params[':f_inicio'] = $fecha_inicio;
            $params[':f_fin'] = $fecha_fin;
        }

        if ($busqueda) {
            $sql .= " AND (c.nombre LIKE :busqueda OR u.nombre LIKE :busqueda OR v.id LIKE :busqueda)";
            $params[':busqueda'] = "%" . trim($busqueda) . "%";
        }

        $sql .= " ORDER BY v.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5. ANULAR VENTA (Con Kardex)
    public function anular($id_venta, $motivo, $id_usuario_admin) {
        try {
            $this->conn->beginTransaction();

            $sqlCheck = "SELECT estado, sucursal_id FROM ventas WHERE id = :id";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->execute([':id' => $id_venta]);
            $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            $estado = $row['estado'];
            $sucursal_id = $row['sucursal_id'];

            if ($estado == 'anulada') {
                throw new Exception("La venta ya está anulada.");
            }

            // Obtener productos
            $detalles = $this->obtenerDetalles($id_venta);
            
            $sqlStock = "UPDATE inventario_sucursales SET stock_actual = stock_actual + :cant WHERE variante_id = :vid AND sucursal_id = :sid";
            $stmtStock = $this->conn->prepare($sqlStock);

            // Consulta para el Kardex (ENTRADA por Devolución)
            $sqlKardex = "INSERT INTO kardex (variante_id, tipo, cantidad, descripcion, usuario_id, fecha, sucursal_id) 
                          VALUES (:vid, 'entrada', :cant, :desc, :uid, NOW(), :sid)";
            $stmtKardex = $this->conn->prepare($sqlKardex);

            foreach ($detalles as $item) {
                // 1. Devolver Stock
                $stmtStock->execute([
                    ':cant' => $item['cantidad'],
                    ':vid' => $item['variante_id'],
                    ':sid' => $sucursal_id
                ]);

                // 2. Registrar en KARDEX
                $stmtKardex->execute([
                    ':vid' => $item['variante_id'],
                    ':cant' => $item['cantidad'],
                    ':desc' => "Anulación Ticket #" . str_pad($id_venta, 6, '0', STR_PAD_LEFT) . " ($motivo)",
                    ':uid' => $id_usuario_admin,
                    ':sid' => $sucursal_id
                ]);
            }

            // Actualizar Estado
            $sqlUpdate = "UPDATE ventas SET 
                          estado = 'anulada', 
                          motivo_anulacion = :motivo, 
                          fecha_anulacion = NOW(), 
                          usuario_anulacion_id = :uid 
                          WHERE id = :id";
                          
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->execute([
                ':motivo' => $motivo,
                ':uid' => $id_usuario_admin,
                ':id' => $id_venta
            ]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}