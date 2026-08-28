<?php
// app/models/Compra.php
require_once '../app/core/Database.php';

class Compra {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function registrar($datos) {
        try {
            // Iniciar Transacción
            $this->conn->beginTransaction();

            // 1. INSERTAR EN TABLA 'compras'
            // Correcciones: 
            // - Usamos 'numero_comprobante'
            // - Quitamos 'estado' porque no existe en tu tabla
            $sql = "INSERT INTO compras (proveedor_id, usuario_id, numero_comprobante, fecha, total, sucursal_id) 
                    VALUES (:prov, :user, :comp, NOW(), :total, :suc)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':prov' => $datos['proveedor_id'],
                ':user' => $datos['usuario_id'],
                ':comp' => $datos['comprobante'],
                ':total' => $datos['total'],
                ':suc' => $datos['sucursal_id']
            ]);
            
            $compra_id = $this->conn->lastInsertId();

            // 2. INSERTAR EN TABLA 'compra_detalles'
            // Correcciones:
            // - Nombre de tabla: 'compra_detalles'
            // - Nombre de columna: 'precio_compra' (en lugar de precio_unitario)
            $sqlDetalle = "INSERT INTO compra_detalles (compra_id, variante_id, cantidad, precio_compra, subtotal) 
                           VALUES (:cid, :vid, :cant, :precio, :sub)";
            
            $stmtDetalle = $this->conn->prepare($sqlDetalle);

            // Preparar actualizaciones de inventario y costos
            $sqlUpdateStock = $this->conn->prepare("UPDATE inventario_sucursales SET stock_actual = stock_actual + :cant WHERE variante_id = :vid AND sucursal_id = :suc");
            
            // Actualizar precio de compra en el producto padre
            $sqlUpdatePrecio = $this->conn->prepare("UPDATE productos SET precio_compra = :precio WHERE id = (SELECT producto_id FROM producto_variantes WHERE id = :vid)");

            // Preparar insert en kardex
            $sqlKardex = "INSERT INTO kardex (variante_id, tipo, cantidad, descripcion, usuario_id, fecha, sucursal_id) 
                          VALUES (:vid, 'entrada', :cant, :desc, :uid, NOW(), :suc)";
            $stmtKardex = $this->conn->prepare($sqlKardex);

            foreach ($datos['carrito'] as $item) {
                // a) Guardar detalle
                $stmtDetalle->execute([
                    ':cid' => $compra_id,
                    ':vid' => $item['variante_id'],
                    ':cant' => $item['cantidad'],
                    ':precio' => $item['costo'], // Aquí pasamos el costo unitario
                    ':sub' => $item['cantidad'] * $item['costo']
                ]);

                // b) Aumentar Stock en la sucursal correspondiente
                $sqlUpdateStock->execute([
                    ':cant' => $item['cantidad'],
                    ':vid' => $item['variante_id'],
                    ':suc' => $datos['sucursal_id']
                ]);

                // Registrar en el Kardex
                $stmtKardex->execute([
                    ':vid' => $item['variante_id'],
                    ':cant' => $item['cantidad'],
                    ':desc' => "Ingreso por Compra #" . str_pad($compra_id, 6, '0', STR_PAD_LEFT) . " / Factura: " . $datos['comprobante'],
                    ':uid' => $datos['usuario_id'],
                    ':suc' => $datos['sucursal_id']
                ]);

                // c) Actualizar Costo en Producto Padre (si es mayor a 0)
                if ($item['costo'] > 0) {
                    $sqlUpdatePrecio->execute([
                        ':precio' => $item['costo'],
                        ':vid' => $item['variante_id']
                    ]);
                }
            }

            // Confirmar todo
            $this->conn->commit();
            return ['status' => true, 'id' => $compra_id];

        } catch (Exception $e) {
            // Si falla, deshacer y mostrar el error real
            $this->conn->rollBack();
            return ['status' => false, 'message' => 'Error SQL: ' . $e->getMessage()];
        }
    }
}