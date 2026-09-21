<?php
// app/models/Producto.php
require_once '../app/core/Database.php';

class Producto {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // 1. LISTAR (CORREGIDO: Ahora incluye precio_compra e imagen)
    public function listar($sucursal_id) {
        $sql = "SELECT p.id, p.nombre, p.precio_compra, p.precio_venta, p.categoria_id, p.activo, p.imagen,
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
    public function registrar($datos, $variantes, $sucursal_activa_id) {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO productos (nombre, codigo_barras_base, categoria_id, precio_compra, precio_venta, descripcion, imagen, activo) 
                    VALUES (:nom, :cod, :cat, :p_compra, :p_venta, :desc, :img, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':nom' => $datos['nombre'],
                ':cod' => !empty($datos['codigo']) ? $datos['codigo'] : null,
                ':cat' => $datos['categoria'],
                ':p_compra' => !empty($datos['precio_compra']) ? $datos['precio_compra'] : 0,
                ':p_venta' => $datos['precio_venta'],
                ':desc' => $datos['descripcion'],
                ':img' => !empty($datos['imagen']) ? $datos['imagen'] : null
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
            return $producto_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception($e->getMessage());
        }
    }

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
                    precio_compra = :pcompra, precio_venta = :pventa, descripcion = :desc" . 
                    (!empty($datos['imagen']) ? ", imagen = :img " : " ") . 
                    "WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            
            $params = [
                ':nombre' => $datos['nombre'], ':cod' => $datos['codigo'], ':cat' => $datos['categoria'],
                ':pcompra' => $datos['precio_compra'], ':pventa' => $datos['precio_venta'],
                ':desc' => $datos['descripcion'], ':id' => $id
            ];
            if (!empty($datos['imagen'])) {
                $params[':img'] = $datos['imagen'];
            }
            $stmt->execute($params);

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

    // 7. OBTENER IMÁGENES DEL PRODUCTO
    public function obtenerImagenes($producto_id) {
        $sql = "SELECT * FROM producto_imagenes WHERE producto_id = :pid ORDER BY es_principal DESC, id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':pid' => $producto_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 8. AGREGAR IMAGEN A LA GALERÍA
    public function agregarImagen($producto_id, $ruta, $es_principal = 0) {
        if ($es_principal == 1) {
            $this->conn->prepare("UPDATE producto_imagenes SET es_principal = 0 WHERE producto_id = :pid")->execute([':pid' => $producto_id]);
            $this->conn->prepare("UPDATE productos SET imagen = :img WHERE id = :pid")->execute([':img' => $ruta, ':pid' => $producto_id]);
        }
        $sql = "INSERT INTO producto_imagenes (producto_id, ruta_imagen, es_principal) VALUES (:pid, :ruta, :p)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':pid' => $producto_id, ':ruta' => $ruta, ':p' => $es_principal]);
    }

    // 9. ESTABLECER IMAGEN COMO PRINCIPAL
    public function establecerPrincipal($producto_id, $imagen_id) {
        $stmt = $this->conn->prepare("SELECT ruta_imagen FROM producto_imagenes WHERE id = :id AND producto_id = :pid");
        $stmt->execute([':id' => $imagen_id, ':pid' => $producto_id]);
        $img = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($img) {
            $this->conn->prepare("UPDATE producto_imagenes SET es_principal = 0 WHERE producto_id = :pid")->execute([':pid' => $producto_id]);
            $this->conn->prepare("UPDATE producto_imagenes SET es_principal = 1 WHERE id = :id")->execute([':id' => $imagen_id]);
            $this->conn->prepare("UPDATE productos SET imagen = :ruta WHERE id = :pid")->execute([':ruta' => $img['ruta_imagen'], ':pid' => $producto_id]);
            return true;
        }
        return false;
    }

    // 10. ELIMINAR IMAGEN DE LA GALERÍA
    public function eliminarImagen($imagen_id, $producto_id) {
        $stmt = $this->conn->prepare("SELECT * FROM producto_imagenes WHERE id = :id AND producto_id = :pid");
        $stmt->execute([':id' => $imagen_id, ':pid' => $producto_id]);
        $img = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($img) {
            $ruta_fisica = '../public/' . $img['ruta_imagen'];
            if (file_exists($ruta_fisica)) {
                @unlink($ruta_fisica);
            }
            $this->conn->prepare("DELETE FROM producto_imagenes WHERE id = :id")->execute([':id' => $imagen_id]);

            if ($img['es_principal'] == 1) {
                $stmtNext = $this->conn->prepare("SELECT * FROM producto_imagenes WHERE producto_id = :pid ORDER BY id ASC LIMIT 1");
                $stmtNext->execute([':pid' => $producto_id]);
                $nextImg = $stmtNext->fetch(PDO::FETCH_ASSOC);

                if ($nextImg) {
                    $this->establecerPrincipal($producto_id, $nextImg['id']);
                } else {
                    $this->conn->prepare("UPDATE productos SET imagen = NULL WHERE id = :pid")->execute([':pid' => $producto_id]);
                }
            }
            return true;
        }
        return false;
    }

    // 11. OBTENER PRODUCTOS PARA LA TIENDA PÚBLICA
    public function obtenerProductosTienda($categoria_id = null, $busqueda = null, $limite = null) {
        $sql = "SELECT p.id, p.nombre, p.descripcion, p.codigo_barras_base, p.precio_venta, p.categoria_id, p.imagen,
                c.nombre as categoria_nombre,
                (SELECT IFNULL(SUM(inv.stock_actual), 0) 
                 FROM inventario_sucursales inv 
                 JOIN producto_variantes v ON inv.variante_id = v.id 
                 WHERE v.producto_id = p.id) as stock_total
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.activo = 1 AND (p.categoria_id IS NULL OR c.activo = 1)";
        
        $params = [];
        if (!empty($categoria_id)) {
            $sql .= " AND p.categoria_id = :cat";
            $params[':cat'] = $categoria_id;
        }
        if (!empty($busqueda)) {
            $sql .= " AND (p.nombre LIKE :q OR p.descripcion LIKE :q OR c.nombre LIKE :q)";
            $params[':q'] = '%' . $busqueda . '%';
        }

        $sql .= " ORDER BY p.id DESC";

        if (!empty($limite)) {
            $sql .= " LIMIT " . intval($limite);
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Adjuntar imágenes de galería y variantes a cada producto
        foreach ($productos as &$p) {
            $p['imagenes'] = $this->obtenerImagenes($p['id']);
            $p['variantes'] = $this->obtenerVariantes($p['id'], 1);
        }

        return $productos;
    }

    // 12. DETALLE DE PRODUCTO CON VARIANTES E IMÁGENES PARA LA TIENDA
    public function obtenerDetalleTienda($id) {
        $sql = "SELECT p.id, p.nombre, p.descripcion, p.codigo_barras_base, p.precio_venta, p.categoria_id, p.imagen,
                c.nombre as categoria_nombre,
                (SELECT IFNULL(SUM(inv.stock_actual), 0) 
                 FROM inventario_sucursales inv 
                 JOIN producto_variantes v ON inv.variante_id = v.id 
                 WHERE v.producto_id = p.id) as stock_total
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.id = :id AND p.activo = 1 AND (p.categoria_id IS NULL OR c.activo = 1)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($prod) {
            $prod['imagenes'] = $this->obtenerImagenes($prod['id']);
            $prod['variantes'] = $this->obtenerVariantes($prod['id'], 1);
        }

        return $prod;
    }
}