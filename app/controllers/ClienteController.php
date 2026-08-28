<?php
require_once '../app/models/Cliente.php';

class ClienteController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/index');
            exit;
        }
    }

    public function index() {
        $clienteModel = new Cliente();
        $termino = trim($_GET['buscar'] ?? '');
        $clientes = $clienteModel->listarFiltrado($termino);
        require_once '../app/views/clientes/index.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $clienteModel = new Cliente();
            $id = $_POST['id'] ?? '';
            $datos = [
                'nombre' => $_POST['nombre'],
                'documento' => $_POST['documento'],
                'telefono' => $_POST['telefono'],
                'correo' => $_POST['correo'],
                'direccion' => $_POST['direccion']
            ];

            if (empty($id)) {
                $clienteModel->registrar($datos);
            } else {
                $clienteModel->actualizar($id, $datos);
            }
            header('Location: ' . BASE_URL . '/cliente/index?msg=success');
        }
    }

    // API JSON para el Modal de Edición
    public function obtener($id) {
        $clienteModel = new Cliente();
        echo json_encode($clienteModel->obtenerPorId($id));
    }

    // API JSON para buscar desde el TPV
    public function buscar($termino) {
        $clienteModel = new Cliente();
        echo json_encode($clienteModel->buscar($termino));
    }

    public function exportarExcel() {
        $clienteModel = new Cliente();
        $termino = trim($_GET['buscar'] ?? '');
        $clientes = $clienteModel->listarFiltrado($termino);

        require_once '../app/core/Database.php';
        $db = new Database();
        $conn = $db->getConnection();
        $stmtEmpresa = $conn->query("SELECT * FROM empresa WHERE id = 1");
        $empresaGlobal = $stmtEmpresa->fetch(PDO::FETCH_ASSOC);
        $nombreEmpresa = $empresaGlobal['nombre'] ?? 'Sistema Moda';

        $titulo = "Directorio de Clientes" . ($termino ? " (Filtro: $termino)" : "");

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=Reporte_Clientes_" . date('Ymd_His') . ".xls");
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
        echo '<th style="padding: 10px; border: 1px solid #ddd;">Nombre / Razón Social</th>';
        echo '<th style="padding: 10px; border: 1px solid #ddd;">Documento</th>';
        echo '<th style="padding: 10px; border: 1px solid #ddd;">Teléfono</th>';
        echo '<th style="padding: 10px; border: 1px solid #ddd;">Email</th>';
        echo '</tr>';

        if (empty($clientes)) {
            echo '<tr><td colspan="5" style="text-align: center; padding: 20px;">No hay clientes registrados.</td></tr>';
        } else {
            foreach ($clientes as $c) {
                echo '<tr>';
                echo '<td style="text-align: center; border: 1px solid #ddd;">' . $c['id'] . '</td>';
                echo '<td style="border: 1px solid #ddd; font-weight: bold;">' . htmlspecialchars($c['nombre']) . '</td>';
                echo '<td style="text-align: center; border: 1px solid #ddd;">' . htmlspecialchars($c['documento'] ?? '-') . '</td>';
                echo '<td style="text-align: center; border: 1px solid #ddd;">' . htmlspecialchars($c['telefono'] ?? '-') . '</td>';
                echo '<td style="border: 1px solid #ddd;">' . htmlspecialchars($c['correo'] ?? '-') . '</td>';
                echo '</tr>';
            }
        }

        echo '</table>';
        echo '</body>';
        echo '</html>';
    }
}