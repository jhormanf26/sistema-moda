<?php
// app/models/Producto.php
require_once '../app/core/Database.php';

class Producto {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // 1. LISTAR (CORREGIDO: Ahora incluye precio_compra)
    // 1. LISTAR (CORREGIDO: Ahora incluye precio_compra)
    public function listar($sucursal_id) {
        $sql = "SELECT p.id, p.nombre, p.precio_compra, p.precio_venta, p.categoria_id, p.activo,
                c.nombre as categoria_nombre, 
                (SELECT IFNULL(SUM(inv.stock_actual), 0) FROM inventario_sucursales inv 
                 JOIN producto_variantes v ON inv.variante_id = v.id 
                 WHERE v.producto_id = p.id AND inv.sucursal_id = :sucursal) as stock_total
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':sucursal' => $sucursal_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. REGISTRAR (Producto + Variantes)
    // 2. REGISTRAR (Producto + Variantes)
    public function registrar($datos, $variantes, $sucursal_activa_id) {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO productos (nombre, codigo_barras_base, categoria_id, precio_compra, precio_venta, descripcion, activo) 
                    VALUES (:nom, :cod, :cat, :p_compra, :p_venta, :desc, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':nom' => $datos['nombre'],
                ':cod' => !empty($datos['codigo']) ? $datos['codigo'] : null,
                ':cat' => $datos['categoria'],
                ':p_compra' => !empty($datos['precio_compra']) ? $datos['precio_compra'] : 0,
                ':p_venta' => $datos['precio_venta'],
                ':desc' => $datos['descripcion']
            ]);
            
            $producto_id = $this->conn->lastInsertId();

            $sqlVar = "INSERT INTO producto_variantes (producto_id, talla, color, codigo_barras_variante) 
                       VALUES (:pid, :talla, :color, :cod_var)";
            $stmtVar = $this->conn->prepare($sqlVar);

            // Obtener todas las sucursales para inicializar stock
            $stmtSuc = $this->conn->query("SELECT id FROM sucursales WHERE activo = 1");
            $sucursales = $stmtSuc->fetchAll(PDO::FETCH_COLUMN);

            $sqlInv = "INSERT INTO inventario_sucursales (sucursal_id, variante_id, stock_actual, stock_minimo) 
                       VALUES (:sid, :vid, :stock, 5)";
            $stmtInv = $this->conn->prepare($sqlInv);

            foreach($variantes as $v) {
                $codigo_var = !empty($v['codigo']) ? $v['codigo'] : $producto_id . '-' . $v['talla'] . '-' . substr($v['color'], 0, 3);
                $stmtVar->execute([
                    ':pid' => $producto_id,
                    ':talla' => $v['talla'],
                    ':color' => $v['color'],
                    ':cod_var' => strtoupper($codigo_var)
                ]);
                $variante_id = $this->conn->lastInsertId();

                foreach($sucursales as $s_id) {
                    $stock_inicial = ($s_id == $sucursal_activa_id) ? $v['stock'] : 0;
                    $stmtInv->execute([
                        ':sid' => $s_id,
                        ':vid' => $variante_id,
                        ':stock' => $stock_inicial
                    ]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    // 3. OBTENER VARIANTES
    // 3. OBTENER VARIANTES
    public function obtenerVariantes($id_producto, $sucursal_id) {
        $sql = "SELECT v.id, v.talla, v.color, v.codigo_barras_variante, IFNULL(inv.stock_actual, 0) as stock_actual 
                FROM producto_variantes v
                LEFT JOIN inventario_sucursales inv ON v.id = inv.variante_id AND inv.sucursal_id = :sid
                WHERE v.producto_id = :pid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':pid' => $id_producto, ':sid' => $sucursal_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. OBTENER UN PRODUCTO
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM productos WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 5. ACTUALIZAR
    public function actualizar($id, $datos, $variantes_update = [], $sucursal_id = 1) {
        try {
            $this->conn->beginTransaction();

            $sql = "UPDATE productos SET 
                    nombre = :nombre, codigo_barras_base = :cod, categoria_id = :cat, 
                    precio_compra = :pcompra, precio_venta = :pventa, descripcion = :desc 
                    WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':nombre' => $datos['nombre'], ':cod' => $datos['codigo'], ':cat' => $datos['categoria'],
                ':pcompra' => $datos['precio_compra'], ':pventa' => $datos['precio_venta'],
                ':desc' => $datos['descripcion'], ':id' => $id
            ]);

            if (!empty($variantes_update)) {
                $sqlVar = "UPDATE producto_variantes SET codigo_barras_variante = :cod 
                           WHERE id = :vid AND producto_id = :pid";
                $stmtVar = $this->conn->prepare($sqlVar);

                $sqlInv = "UPDATE inventario_sucursales SET stock_actual = :stock 
                           WHERE variante_id = :vid AND sucursal_id = :sid";
                $stmtInv = $this->conn->prepare($sqlInv);

                foreach ($variantes_update as $v) {
                    $stmtVar->execute([
                        ':cod' => $v['codigo'], ':vid' => $v['id_variante'], ':pid' => $id
                    ]);
                    // Puede que no exista el registro aún si se creó en otra sucursal, usemos INSERT...ON DUPLICATE KEY UPDATE o solo UPDATE asumiendo que ya existen
                    $stmtInv->execute([
                        ':stock' => $v['stock'], ':vid' => $v['id_variante'], ':sid' => $sucursal_id
                    ]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // 6. CAMBIAR ESTADO
    public function cambiarEstado($id, $nuevo_estado) {
        $sql = "UPDATE productos SET activo = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':estado' => $nuevo_estado, ':id' => $id]);
    }
}