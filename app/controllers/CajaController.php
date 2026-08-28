<?php
// app/controllers/CajaController.php
require_once '../app/models/Caja.php';
require_once '../app/models/Gasto.php'; // Importante

class CajaController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/index');
            exit;
        }
    }

    // INDEX: Determina si abrir o cerrar
    public function index() {
        $cajaModel = new Caja();
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
        $caja = $cajaModel->obtenerCajaAbierta($_SESSION['user_id'], $sucursal_id);

        if ($caja) {
            // --- PANTALLA DE CIERRE ---
            
            // 1. Ventas
            $ventas_sesion = $cajaModel->calcularVentasSesion($_SESSION['user_id'], $caja['fecha_apertura'], $sucursal_id);
            
            // 2. Gastos (Nuevo)
            $gastoModel = new Gasto();
            $gastos_sesion = $gastoModel->totalGastosSesion($caja['id']);

            require_once '../app/views/caja/cerrar.php';
        } else {
            // --- PANTALLA DE APERTURA ---
            require_once '../app/views/caja/abrir.php';
        }
    }

    // PROCESO ABRIR
    public function abrir() {
        $monto = $_POST['monto'];
        $cajaModel = new Caja();
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
        
        if ($cajaModel->abrir($_SESSION['user_id'], $monto, $sucursal_id)) {
            header('Location: ' . BASE_URL . '/ventas');
        } else {
            echo "Error al abrir caja";
        }
    }

    // PROCESO CERRAR (Con Gastos)
    public function cerrar() {
        $id_sesion = $_POST['id_sesion'];
        $monto_fisico = $_POST['monto_fisico'];
        $total_ventas = $_POST['total_ventas'];
        $total_gastos = $_POST['total_gastos']; // Nuevo dato recibido

        $cajaModel = new Caja();
        
        if ($cajaModel->cerrar($id_sesion, $monto_fisico, $total_ventas, $total_gastos)) {
            // Al cerrar, cerramos sesión del sistema por seguridad
            header('Location: ' . BASE_URL . '/auth/logout');
        } else {
            echo "Error al cerrar caja";
        }
    }
}