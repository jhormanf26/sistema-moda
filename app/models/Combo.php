<?php
// app/models/Combo.php
require_once '../app/core/Database.php';

class Combo {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Listar todos los combos para el panel administrativo
     */
    public function listar() {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM combo_items WHERE combo_id = c.id) as total_items
                FROM combos c 
                ORDER BY c.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $combos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($combos as &$combo) {
            $combo['items'] = $this->obtenerItemsCombo($combo['id']);
        }

        return $combos;
    }

    /**
     * Obtener un combo por su ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM combos WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        $combo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($combo) {
            $combo['items'] = $this->obtenerItemsCombo($combo['id']);
        }

        return $combo;
    }

    /**
     * Obtener los ítems (productos y cantidades) de un combo
     */
    public function obtenerItemsCombo($combo_id) {
        $sql = "SELECT ci.*, p.nombre as producto_nombre, p.precio_venta, p.imagen as producto_imagen, p.activo as producto_activo,
                (SELECT IFNULL(SUM(inv.stock_actual), 0) 
                 FROM inventario_sucursales inv 
                 JOIN producto_variantes v ON inv.variante_id = v.id 
                 WHERE v.producto_id = p.id) as stock_total_producto
                FROM combo_items ci
                JOIN productos p ON ci.producto_id = p.id
                WHERE ci.combo_id = :cid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':cid' => $combo_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Registrar un nuevo combo
     */
    public function registrar($datos, $items) {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO combos (nombre, descripcion, precio, imagen, activo) 
                    VALUES (:nombre, :desc, :precio, :img, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':desc'   => $datos['descripcion'] ?? null,
                ':precio' => $datos['precio'],
                ':img'    => !empty($datos['imagen']) ? $datos['imagen'] : null
            ]);

            $combo_id = $this->conn->lastInsertId();

            $sqlItem = "INSERT INTO combo_items (combo_id, producto_id, cantidad) VALUES (:cid, :pid, :cant)";
            $stmtItem = $this->conn->prepare($sqlItem);

            foreach ($items as $item) {
                if (!empty($item['producto_id']) && !empty($item['cantidad'])) {
                    $stmtItem->execute([
                        ':cid'  => $combo_id,
                        ':pid'  => intval($item['producto_id']),
                        ':cant' => intval($item['cantidad'])
                    ]);
                }
            }

            $this->conn->commit();
            return $combo_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Actualizar un combo existente
     */
    public function actualizar($id, $datos, $items) {
        try {
            $this->conn->beginTransaction();

            $sql = "UPDATE combos SET nombre = :nombre, descripcion = :desc, precio = :precio" .
                    (!empty($datos['imagen']) ? ", imagen = :img " : " ") .
                    "WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            
            $params = [
                ':nombre' => $datos['nombre'],
                ':desc'   => $datos['descripcion'] ?? null,
                ':precio' => $datos['precio'],
                ':id'     => $id
            ];
            if (!empty($datos['imagen'])) {
                $params[':img'] = $datos['imagen'];
            }
            $stmt->execute($params);

            // Reemplazar los ítems del combo
            $stmtDel = $this->conn->prepare("DELETE FROM combo_items WHERE combo_id = :cid");
            $stmtDel->execute([':cid' => $id]);

            $sqlItem = "INSERT INTO combo_items (combo_id, producto_id, cantidad) VALUES (:cid, :pid, :cant)";
            $stmtItem = $this->conn->prepare($sqlItem);

            foreach ($items as $item) {
                if (!empty($item['producto_id']) && !empty($item['cantidad'])) {
                    $stmtItem->execute([
                        ':cid'  => $id,
                        ':pid'  => intval($item['producto_id']),
                        ':cant' => intval($item['cantidad'])
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

    /**
     * Cambiar estado activo / inactivo de un combo
     */
    public function cambiarEstado($id, $nuevo_estado) {
        $sql = "UPDATE combos SET activo = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':estado' => $nuevo_estado, ':id' => $id]);
    }

    /**
     * Eliminar un combo
     */
    public function eliminar($id) {
        $stmt = $this->conn->prepare("DELETE FROM combos WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Obtener los combos activos para la tienda pública con cálculo dinámico de stock
     */
    public function obtenerCombosTienda() {
        $sql = "SELECT c.* FROM combos c WHERE c.activo = 1 ORDER BY c.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $combos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($combos as &$combo) {
            $sqlItems = "SELECT ci.cantidad, p.id as producto_id, p.nombre as producto_nombre, 
                         p.precio_venta, p.imagen as producto_imagen, p.activo as producto_activo,
                         c.activo as categoria_activa,
                         (SELECT IFNULL(SUM(inv.stock_actual), 0) 
                          FROM inventario_sucursales inv 
                          JOIN producto_variantes v ON inv.variante_id = v.id 
                          WHERE v.producto_id = p.id) as stock_total_producto
                         FROM combo_items ci
                         JOIN productos p ON ci.producto_id = p.id
                         LEFT JOIN categorias c ON p.categoria_id = c.id
                         WHERE ci.combo_id = :cid";
            $stmtItems = $this->conn->prepare($sqlItems);
            $stmtItems->execute([':cid' => $combo['id']]);
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            $combo['items'] = $items;
            $combo['estado_stock'] = 'disponible';
            $precioOriginalTotal = 0;

            foreach ($items as $it) {
                $precioOriginalTotal += ($it['precio_venta'] * $it['cantidad']);
                // Si el producto está inactivo, o su categoría está inactiva (y tiene categoría), o su stock es menor al requerido por el combo
                if ($it['producto_activo'] == 0 || ($it['categoria_activa'] !== null && $it['categoria_activa'] == 0) || $it['stock_total_producto'] < $it['cantidad']) {
                    $combo['estado_stock'] = 'agotado';
                }
            }

            $combo['precio_original_total'] = $precioOriginalTotal;
            $combo['ahorro'] = max(0, $precioOriginalTotal - $combo['precio']);
        }

        return $combos;
    }
}
