<?php
// app/controllers/HomeController.php

require_once '../app/models/Reporte.php';

class HomeController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/index');
            exit;
        }
    }

    public function index() {
        $reporteModel = new Reporte();
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;

        // Obtener todos los datos necesarios para el Dashboard (KPIs, Gráficos)
        $hoy = $reporteModel->ventasHoy($sucursal_id);
        $mes = $reporteModel->ventasDelMes($sucursal_id);
        $historial = $reporteModel->ventasUltimos7Dias($sucursal_id);
        $topProductos = $reporteModel->productosMasVendidos($sucursal_id);
        $totalProductos = $reporteModel->totalProductosActivos();
        $alertasStock = $reporteModel->alertasBajoStock($sucursal_id);

        // Convertir datos a formato JSON para que JavaScript los pueda leer fácilmente
        $dataHistorial = json_encode($historial);
        $dataTop = json_encode($topProductos);

        require_once '../app/views/home/index.php';
    }
}