<?php
// app/controllers/ProductoController.php
require_once '../app/models/Producto.php';

class ProductoController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/index');
            exit;
        }
    }

    // 1. LISTAR (Público)
    public function index() {
        $productoModel = new Producto();
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
        $productos = $productoModel->listar($sucursal_id);
        require_once '../app/views/productos/index.php';
    }

    // 2. CREAR (Solo Admin)
    public function crear() {
        if ($_SESSION['user_rol'] != 'admin') { header('Location: ' . BASE_URL . '/producto/index'); return; }
        
        require_once '../app/models/Categoria.php';
        $categoriaModel = new Categoria();
        $categorias = $categoriaModel->listarActivas();
        
        require_once '../app/views/productos/crear.php';
    }

    // 3. GUARDAR (Solo Admin)
    public function guardar() {
        if ($_SESSION['user_rol'] != 'admin') { return; }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $productoModel = new Producto();
            
            $datos = [
                'nombre' => $_POST['nombre'], 'codigo' => $_POST['codigo'] ?? '', 'categoria' => $_POST['categoria'],
                'precio_compra' => $_POST['precio_compra'] ?? 0, 'precio_venta' => $_POST['precio_venta'], 'descripcion' => $_POST['descripcion'] ?? ''
            ];

            $variantes = [];
            if(isset($_POST['talla'])) {
                for($i = 0; $i < count($_POST['talla']); $i++) {
                    if(!empty($_POST['talla'][$i]) && !empty($_POST['color'][$i])) {
                        $variantes[] = [
                            'talla' => $_POST['talla'][$i], 'color' => $_POST['color'][$i],
                            'stock' => $_POST['stock'][$i], 'codigo' => $_POST['codigo_var'][$i] ?? ''
                        ];
                    }
                }
            }

            try {
                $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
                $productoModel->registrar($datos, $variantes, $sucursal_id);
                header('Location: ' . BASE_URL . '/producto/index?msg=success');
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    }

    // 4. API VARIANTES (Público)
    public function obtenerVariantes($id) {
        if(empty($id)) { echo json_encode([]); return; }
        $productoModel = new Producto();
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
        $datos = $productoModel->obtenerVariantes($id, $sucursal_id);
        header('Content-Type: application/json');
        echo json_encode($datos);
    }

    // 5. VISTA EDITAR (Solo Admin)
    public function editar($id) {
        if ($_SESSION['user_rol'] != 'admin') { header('Location: ' . BASE_URL . '/producto/index'); return; }

        $productoModel = new Producto();
        $p = $productoModel->obtenerPorId($id);
        
        if (!$p) { header('Location: ' . BASE_URL . '/producto/index'); return; }
        
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
        $variantes = $productoModel->obtenerVariantes($id, $sucursal_id);

        require_once '../app/models/Categoria.php';
        $categoriaModel = new Categoria();
        $categorias = $categoriaModel->listarActivas();

        require_once '../app/views/productos/editar.php';
    }

    // 6. PROCESAR ACTUALIZACIÓN (Solo Admin)
    public function actualizar() {
        if ($_SESSION['user_rol'] != 'admin') { return; }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $datos = [
                'nombre' => $_POST['nombre'], 'codigo' => $_POST['codigo'], 'categoria' => $_POST['categoria'],
                'precio_compra' => $_POST['precio_compra'], 'precio_venta' => $_POST['precio_venta'], 'descripcion' => $_POST['descripcion']
            ];

            $variantes_update = [];
            if(isset($_POST['var_id'])) {
                for($i = 0; $i < count($_POST['var_id']); $i++) {
                    $variantes_update[] = [
                        'id_variante' => $_POST['var_id'][$i], 'stock' => $_POST['var_stock'][$i], 'codigo' => $_POST['codigo_var'][$i] ?? ''
                    ];
                }
            }

            $productoModel = new Producto();
            $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
            if ($productoModel->actualizar($id, $datos, $variantes_update, $sucursal_id)) {
                header('Location: ' . BASE_URL . '/producto/index?msg=updated');
            } else {
                echo "Error al actualizar.";
            }
        }
    }

    // 7. CAMBIAR ESTADO (Solo Admin)
    public function cambiarEstado($id, $estadoActual) {
        if ($_SESSION['user_rol'] != 'admin') { header('Location: ' . BASE_URL . '/producto/index'); return; }

        $productoModel = new Producto();
        $nuevoEstado = ($estadoActual == 1) ? 0 : 1;
        
        if ($productoModel->cambiarEstado($id, $nuevoEstado)) {
            header('Location: ' . BASE_URL . '/producto/index?msg=status_changed');
        } else {
            echo "Error al cambiar estado.";
        }
    }

    // 8. EXPORTAR A EXCEL (Solo Admin)
    public function exportar() {
        if ($_SESSION['user_rol'] != 'admin') { header('Location: ' . BASE_URL . '/producto/index'); return; }

        ob_clean(); 
        $productoModel = new Producto();
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
        $productos = $productoModel->listar($sucursal_id);

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=inventario_" . date('Y-m-d') . ".xls");
        header("Pragma: no-cache"); header("Expires: 0");
        echo "\xEF\xBB\xBF"; 

        echo "<table border='1'>";
        echo "<tr style='background-color: #f0f0f0; font-weight: bold;'>
                <th>ID</th><th>Producto</th><th>Categoria</th><th>Precio Costo</th><th>Precio Venta</th><th>Stock Total</th><th>Estado</th>
              </tr>";

        foreach ($productos as $p) {
            $estado = ($p['activo'] == 1) ? 'Activo' : 'Inactivo';
            $precio_c = number_format($p['precio_compra'] ?? 0, 2);
            $precio_v = number_format($p['precio_venta'] ?? 0, 2);
            
            echo "<tr>
                    <td>{$p['id']}</td><td>{$p['nombre']}</td><td>{$p['categoria_nombre']}</td>
                    <td>{$precio_c}</td><td>{$precio_v}</td><td>{$p['stock_total']}</td><td>{$estado}</td>
                  </tr>";
        }
        echo "</table>";
        exit;
    }

    // 9. API KARDEX (HISTORIAL DE MOVIMIENTOS)
    public function historial($id) {
        // Obtenemos los datos del Kardex (Movimientos)
        require_once '../app/models/Kardex.php';
        $kardexModel = new Kardex();
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
        $movimientos = $kardexModel->obtenerHistorial($id, $sucursal_id);
        
        header('Content-Type: application/json');
        echo json_encode($movimientos);
    }
}