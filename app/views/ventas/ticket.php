<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ticket #<?= $venta['id'] ?></title>
    <style>
        /* Estilos optimizados para impresora térmica (80mm / 58mm) */
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            margin: 0;
            padding: 10px;
            width: 300px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        /* Línea separadora punteada */
        .line {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }

        /* Tabla de productos */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        /* Ocultar botón al imprimir */
        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 0;
                width: 100%;
            }
        }

        .btn-print {
            background: #000;
            color: #fff;
            border: none;
            padding: 10px;
            width: 100%;
            cursor: pointer;
            margin-bottom: 10px;
            font-family: sans-serif;
            font-weight: bold;
        }
    </style>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>

<body>

    <button onclick="window.print()" class="no-print btn-print">🖨️ IMPRIMIR TICKET</button>

    <div class="text-center">
        <?php if (!empty($empresa['logo'])): ?>
            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($empresa['logo']) ?>"
                style="max-height: 60px; max-width: 150px; margin-bottom: 5px;">
        <?php endif; ?>
        <h3 style="margin: 0;"><?= strtoupper($empresa['nombre']) ?></h3>

        <p style="margin: 2px 0;">RUC: <?= $empresa['ruc'] ?></p>
        <p style="margin: 2px 0;"><?= $empresa['direccion'] ?></p>
        <p style="margin: 2px 0;">Tel: <?= $empresa['telefono'] ?></p>
        <?php if (!empty($empresa['email'])): ?>
            <p style="margin: 2px 0; font-size: 10px;"><?= $empresa['email'] ?></p>
        <?php endif; ?>
    </div>

    <div class="line"></div>

    <div style="line-height: 1.5;">
        <strong>Ticket:</strong> #<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?><br>
        <strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?><br>
        <strong>Cajero:</strong> <?= $venta['vendedor'] ?><br>

        <strong>Cliente:</strong> <?= $venta['cliente_nombre'] ?><br>
        <?php if (!empty($venta['cliente_doc']) && $venta['cliente_doc'] != '00000000'): ?>
            <strong>DOC:</strong> <?= $venta['cliente_doc'] ?>
        <?php endif; ?>
    </div>

    <div class="line"></div>

    <table>
        <thead>
            <tr>
                <th style="text-align:left">Desc.</th>
                <th style="text-align:center">Cant.</th>
                <th style="text-align:right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalles as $item): ?>
                <?php 
                $esPredeterminado = (
                    (empty($item['talla']) || in_array($item['talla'], ['Única', 'Unica', 'Estándar', 'Estandar', 'N/A'])) &&
                    (empty($item['color']) || in_array($item['color'], ['Estándar', 'Estandar', 'Único', 'Unico', 'N/A']))
                );
                ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($item['nombre']) ?><br>
                        <?php if (!$esPredeterminado): ?>
                            <small>(<?= htmlspecialchars($item['talla']) ?> - <?= htmlspecialchars($item['color']) ?>)</small>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= $item['cantidad'] ?></td>
                    <td class="text-right"><?= htmlspecialchars($empresa['moneda'] ?? '$') ?>
                        <?= formatearCOP($item['subtotal']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="line"></div>

    <div class="text-right">
        <h2 style="margin: 10px 0;">TOTAL: <?= htmlspecialchars($empresa['moneda'] ?? '$') ?>
            <?= formatearCOP($venta['total']) ?></h2>
    </div>

    <div class="text-center" style="margin-top: 20px;">
        <p><?= nl2br($empresa['mensaje_ticket']) ?></p>
        <p style="font-size: 10px; margin-top: 10px;">Sistema Moda v1.0</p>
    </div>

    <script>
        // Opcional: Auto-imprimir al cargar la ventana
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>
