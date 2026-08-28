<?php
require_once '../app/models/Reporte.php';

class ReporteController
{

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/index');
            exit;
        }
    }

    public function index()
    {
        // En el futuro aquí cargarás las opciones de reportes
        require_once '../app/views/reportes/index.php';
    }
}