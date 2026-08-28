<?php
// app/controllers/GastoController.php
require_once '../app/models/Gasto.php';
require_once '../app/models/Caja.php';

class GastoController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . '/auth/index'); exit; }
    }

    public function index() {
        $cajaModel = new Caja();
        $gastoModel = new Gasto();
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
        
        $caja = $cajaModel->obtenerCajaAbierta($_SESSION['user_id'], $sucursal_id);

        if (!$caja) { header('Location: ' . BASE_URL . '/caja/index'); exit; }

        $fecha_inicio = $_GET['fecha_inicio'] ?? null;
        $fecha_fin = $_GET['fecha_fin'] ?? null;

        // FIX: Obtener total y lista de gastos para la sesión actual
        $gastos = $gastoModel->listarFiltrado($caja['id'], $fecha_inicio, $fecha_fin, $sucursal_id);
        $totalHoy = $gastoModel->totalGastosFiltrado($caja['id'], $fecha_inicio, $fecha_fin, $sucursal_id); 

        require_once '../app/views/gastos/index.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $cajaModel = new Caja();
            $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
            $caja = $cajaModel->obtenerCajaAbierta($_SESSION['user_id'], $sucursal_id);

            if ($caja) {
                $gastoModel = new Gasto();
                $desc = $_POST['descripcion'];
                $monto = $_POST['monto'];
                $usuario_id = $_SESSION['user_id'];
                $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
                
                $gastoModel->registrar($caja['id'], $desc, $monto, $usuario_id, $sucursal_id);
            }
            header('Location: ' . BASE_URL . '/gasto/index?msg=success');
        }
    }

    public function exportarExcel() {
        $cajaModel = new Caja();
        $gastoModel = new Gasto();
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
        
        $caja = $cajaModel->obtenerCajaAbierta($_SESSION['user_id'], $sucursal_id);
        if (!$caja) { header('Location: ' . BASE_URL . '/caja/index'); exit; }

        $fecha_inicio = $_GET['fecha_inicio'] ?? null;
        $fecha_fin = $_GET['fecha_fin'] ?? null;

        $gastos = $gastoModel->listarFiltrado($caja['id'], $fecha_inicio, $fecha_fin, $sucursal_id);
        $totalHoy = $gastoModel->totalGastosFiltrado($caja['id'], $fecha_inicio, $fecha_fin, $sucursal_id); 
        
        require_once '../app/core/Database.php';
        $db = new Database();
        $conn = $db->getConnection();
        $stmtEmpresa = $conn->query("SELECT * FROM empresa WHERE id = 1");
        $empresaGlobal = $stmtEmpresa->fetch(PDO::FETCH_ASSOC);
        $nombreEmpresa = $empresaGlobal['nombre'] ?? 'Sistema Moda';
        $monedaEmpresa = $empresaGlobal['moneda'] ?? 'S/';
        
        $titulo = ($fecha_inicio && $fecha_fin) ? "Gastos (" . date('d/m/Y', strtotime($fecha_inicio)) . " - " . date('d/m/Y', strtotime($fecha_fin)) . ")" : "Gastos del Turno Actual";

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=Reporte_Gastos_" . date('Ymd_His') . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>';
        echo '<body>';
        echo '<table border="1" cellpadding="5" cellspacing="0" style="font-family: Arial, sans-serif; border-collapse: collapse; min-width: 800px;">';
        
        echo '<tr>';
        echo '<td colspan="5" style="text-align: center; font-size: 22px; font-weight: bold; background-color: #2E1F29; color: #F5EEF2; padding: 15px;">';
        echo htmlspecialchars($nombreEmpresa) . "<br><span style='font-size: 16px; color: #C9847A;'>" . htmlspecialchars($titulo) . "</span>";
        echo '</td>';
        echo '</tr>';

        echo '<tr><td colspan="5"></td></tr>';

        echo '<tr style="background-color: #9B7FA6; color: white; font-weight: bold; text-align: center;">';
        echo '<th style="padding: 10px; border: 1px solid #ddd;">ID</th>';
        echo '<th style="padding: 10px; border: 1px solid #ddd;">Fecha/Hora</th>';
        echo '<th style="padding: 10px; border: 1px solid #ddd;">Monto (' . htmlspecialchars($monedaEmpresa) . ')</th>';
        echo '<th style="padding: 10px; border: 1px solid #ddd;">Motivo / Descripción</th>';
        echo '<th style="padding: 10px; border: 1px solid #ddd;">Registrado Por</th>';
        echo '</tr>';

        if (empty($gastos)) {
            echo '<tr><td colspan="5" style="text-align: center; padding: 20px;">No hay gastos registrados.</td></tr>';
        } else {
            foreach ($gastos as $g) {
                echo '<tr>';
                echo '<td style="text-align: center; border: 1px solid #ddd;">#' . str_pad($g['id'], 5, '0', STR_PAD_LEFT) . '</td>';
                echo '<td style="text-align: center; border: 1px solid #ddd;">' . date('d/m/Y H:i', strtotime($g['fecha'])) . '</td>';
                echo '<td style="text-align: right; color: #C0616F; font-weight: bold; border: 1px solid #ddd;">' . number_format($g['monto'], 2) . '</td>';
                echo '<td style="border: 1px solid #ddd;">' . htmlspecialchars($g['descripcion']) . '</td>';
                echo '<td style="text-align: center; border: 1px solid #ddd;">' . htmlspecialchars($g['usuario_nombre']) . '</td>';
                echo '</tr>';
            }
        }

        echo '<tr style="background-color: #F8EFF2; font-weight: bold;">';
        echo '<td colspan="2" style="text-align: right; padding: 10px; color: #3D2030; border: 1px solid #ddd;">TOTAL GASTOS:</td>';
        echo '<td style="text-align: right; padding: 10px; color: #C0616F; font-size: 16px; border: 1px solid #ddd;">' . number_format($totalHoy, 2) . '</td>';
        echo '<td colspan="2" style="border: 1px solid #ddd;"></td>';
        echo '</tr>';

        echo '</table>';
        echo '</body>';
        echo '</html>';
    }
}