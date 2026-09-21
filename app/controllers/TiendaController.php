<?php
// app/controllers/TiendaController.php

require_once '../app/models/Producto.php';
require_once '../app/models/Categoria.php';
require_once '../app/models/Empresa.php';
require_once '../app/models/Sucursal.php';

class TiendaController {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $productoModel = new Producto();
        $categoriaModel = new Categoria();
        $empresaModel = new Empresa();
        $sucursalModel = new Sucursal();

        $categoria_id = isset($_GET['cat']) ? intval($_GET['cat']) : null;
        $busqueda = isset($_GET['q']) ? trim($_GET['q']) : null;

        $categorias = $categoriaModel->listarTodas();
        $productos = $productoModel->obtenerProductosTienda($categoria_id, $busqueda);
        $empresa = $empresaModel->obtener();
        $sucursales = $sucursalModel->listar();

        // Si es una solicitud AJAX para filtrar productos sin recargar
        if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'productos' => $productos,
                'total' => count($productos)
            ]);
            exit;
        }

        require_once '../app/views/tienda/index.php';
    }

    public function detalle($id = null) {
        header('Content-Type: application/json');
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
            exit;
        }

        $productoModel = new Producto();
        $producto = $productoModel->obtenerDetalleTienda(intval($id));

        if ($producto) {
            echo json_encode(['status' => 'success', 'producto' => $producto]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Producto no encontrado']);
        }
        exit;
    }
}
