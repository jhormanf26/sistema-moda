<?php
// app/controllers/EtiquetaController.php
require_once '../app/models/Producto.php';
require_once '../vendor/autoload.php'; 

use Picqer\Barcode\BarcodeGeneratorPNG;
// Eliminamos: use FPDF; (Ya que es una clase global y generaba un Warning innecesario)

class EtiquetaController {

    public function __construct() {
        // CORRECCIÓN: Se cambió 'start()' por 'session_start()'
        if (session_status() === PHP_SESSION_NONE) session_start(); 
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/index');
            exit;
        }
    }

    // 1. VISTA DE SELECCIÓN
    public function index() {
        $productoModel = new Producto();
        $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
        $productos = $productoModel->listar($sucursal_id);
        require_once '../app/views/etiquetas/index.php';
    }

    // 2. GENERAR PDF
    public function generar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['productos'])) {
            
            ob_clean(); // Limpiar buffer

            $seleccionados = $_POST['productos'];
            $cantidades = $_POST['cantidad'];
            
            $productoModel = new Producto();
            
            $pdf = new FPDF('P', 'mm', 'A4'); // FPDF es la clase global
            $pdf->AddPage();
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetAutoPageBreak(true, 10);

            $generator = new BarcodeGeneratorPNG();

            $ancho_etiqueta = 64; 
            $alto_etiqueta = 38;
            $columna = 0; $fila = 0;
            $margen_x = 8; $margen_y = 12;

            foreach ($seleccionados as $id) {
                $prod = $productoModel->obtenerPorId($id);
                $codigo = !empty($prod['codigo_barras_base']) ? $prod['codigo_barras_base'] : str_pad($id, 8, '0', STR_PAD_LEFT);
                
                $cantidad_imprimir = (int)$cantidades[$id];

                for ($i = 0; $i < $cantidad_imprimir; $i++) {
                    
                    $x = $margen_x + ($columna * $ancho_etiqueta);
                    $y = $margen_y + ($fila * $alto_etiqueta);

                    if ($y + $alto_etiqueta > 280) {
                        $pdf->AddPage();
                        $columna = 0; $fila = 0; $y = $margen_y; $x = $margen_x;
                    }

                    // --- DIBUJAR ETIQUETA ---
                    $pdf->Rect($x, $y, $ancho_etiqueta - 2, $alto_etiqueta - 2);

                    // Nombre Producto (Usamos iconv para tildes en lugar de utf8_decode)
                    $pdf->SetXY($x, $y + 2);
                    $pdf->SetFont('Arial', 'B', 8);
                    $nombre_limpio = iconv('UTF-8', 'windows-1252', substr($prod['nombre'], 0, 28));
                    $pdf->Cell($ancho_etiqueta - 2, 5, $nombre_limpio, 0, 1, 'C');

                    // Generar Imagen Barcode en memoria
                    $barcodeData = $generator->getBarcode($codigo, $generator::TYPE_CODE_128, 2, 30);
                    $tempFile = sys_get_temp_dir() . '/bar_' . $id . '.png';
                    file_put_contents($tempFile, $barcodeData);

                    // Pegar Imagen en PDF
                    $pdf->Image($tempFile, $x + 10, $y + 8, 40, 12);

                    // Texto del Código
                    $pdf->SetXY($x, $y + 20);
                    $pdf->SetFont('Courier', '', 9);
                    $pdf->Cell($ancho_etiqueta - 2, 4, $codigo, 0, 1, 'C');

                    // Precio
                    $pdf->SetXY($x, $y + 25);
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->Cell($ancho_etiqueta - 2, 6, 'S/ ' . number_format($prod['precio_venta'], 2), 0, 1, 'C');

                    // Mover contadores
                    $columna++;
                    if ($columna >= 3) {
                        $columna = 0;
                        $fila++;
                    }
                }
            }

            $pdf->Output('I', 'etiquetas.pdf'); 
        }
    }
}