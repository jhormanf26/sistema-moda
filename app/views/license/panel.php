<?php
// app/views/license/panel.php
if (!class_exists('Database')) require_once '../app/core/Database.php';
if (!class_exists('License'))  require_once '../app/core/License.php';

$diasRestantes = null;
$porcentaje    = 0;
$esPerpetua    = false;

if ($licInfo) {
    if (isset($licInfo['fecha_fin']) && !empty($licInfo['fecha_fin'])) {
        $hoy  = new DateTime('today');
        $fin  = new DateTime($licInfo['fecha_fin']);
        $ini  = isset($licInfo['emision']) && $licInfo['emision'] > 0
            ? (new DateTime())->setTimestamp((int)$licInfo['emision'])
            : $hoy;
        
        $diff = $hoy->diff($fin);
        $diasRestantes = (int)$diff->format('%r%a');
        $diasTotales   = max(1, (int)$ini->diff($fin)->days);
        $porcentaje    = max(0, min(100, round(($diasRestantes / $diasTotales) * 100)));
    } else {
        $esPerpetua = true;
        $porcentaje = 100;
    }
}

if (!$licValida) {
    $barColor  = '#fca5a5';
    $estadoTxt = 'EXPIRADA O SUSPENDIDA';
    $badgeCls  = 'danger';
    $iconCls   = 'bi-shield-x-fill';
} elseif ($esPerpetua) {
    $barColor  = '#38bdf8';
    $estadoTxt = 'PERPETUA / ILIMITADA';
    $badgeCls  = 'info';
    $iconCls   = 'bi-shield-lock-fill';
} elseif ($diasRestantes !== null && $diasRestantes <= 7) {
    $barColor  = '#fcd34d';
    $estadoTxt = 'POR VENCER';
    $badgeCls  = 'warning';
    $iconCls   = 'bi-shield-exclamation';
} else {
    $barColor  = '#6ee7b7';
    $estadoTxt = 'ACTIVA';
    $badgeCls  = 'success';
    $iconCls   = 'bi-shield-fill-check';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Licencia – Sistema de Moda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        @media (min-width: 992px) {
            .main-content-area {
                margin-left: 260px;
                width: calc(100% - 260px);
            }
        }
        .lic-header-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #fff;
            border-radius: 12px;
            padding: 1.75rem 2rem 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .lic-bar-track {
            height: 8px; border-radius: 999px;
            background: rgba(255,255,255,.15); overflow: hidden;
        }
        .lic-bar-fill {
            height: 100%; border-radius: 999px;
            transition: width 1.2s ease;
        }
        .info-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: .85rem 1.1rem;
        }
        .info-box .lbl {
            font-size: .7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .06em;
            color: #6c757d; margin-bottom: .2rem;
        }
        .info-box .val {
            font-size: 1rem; font-weight: 700; color: #212529;
        }
        .token-card {
            border: 2px dashed #dee2e6;
            border-radius: 12px;
        }
        .token-card textarea {
            font-family: 'Courier New', monospace;
            font-size: .8rem;
            background: #f8f9fa;
            resize: vertical;
        }
    </style>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body class="bg-light">

    <!-- Navbar móvil -->
    <nav class="navbar navbar-dark bg-dark d-lg-none shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold">Sistema Moda</span>
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                <i class="bi bi-list"></i> Menú
            </button>
        </div>
    </nav>

    <?php include '../app/views/layouts/sidebar.php'; ?>

    <div class="main-content-area" style="height:100vh; overflow-y:auto;">
        <div class="container-fluid p-4">

            <!-- Cabecera de página -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="fw-bold mb-0">
                        <i class="bi bi-shield-lock-fill me-2 text-primary"></i>Mi Licencia
                    </h2>
                    <p class="text-muted small mb-0">Estado, vigencia y revalidación de la licencia del sistema</p>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/license/revalidar" class="btn btn-outline-primary btn-sm fw-bold">
                        <i class="bi bi-arrow-repeat me-1"></i> Revalidar con Servidor
                    </a>
                </div>
            </div>

            <!-- Alertas -->
            <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <span><strong>¡Licencia actualizada!</strong> El sistema ha validado y guardado correctamente la nueva licencia.</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php elseif ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-x-circle-fill fs-5"></i>
                <span><strong>Error:</strong> <?= htmlspecialchars($error) ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="row g-4">

                <!-- ── Estado actual de la licencia ── -->
                <div class="col-12 col-xl-7">
                    <div class="card shadow-sm border-0 h-100" style="border-radius:12px;overflow:hidden;">

                        <!-- Header oscuro con barra -->
                        <div class="lic-header-card">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div>
                                    <div class="text-white-50 small mb-2" style="letter-spacing:.04em;font-size:.85rem;text-transform:uppercase;">Estado del Sistema</div>
                                    <h2 class="fw-bold mb-2" style="font-size: 2.2rem; color: <?= $barColor ?>;">
                                        <i class="bi <?= $iconCls ?> me-2"></i>Licencia <?= ucfirst(strtolower($estadoTxt)) ?>
                                    </h2>
                                    
                                    <?php if ($licInfo && isset($licInfo['fecha_fin'])): ?>
                                    <div class="d-inline-flex align-items-center bg-dark bg-opacity-25 px-4 py-3 rounded-3 mt-2 border border-secondary border-opacity-25 shadow-sm">
                                        <div class="me-4 pe-4 border-end border-secondary border-opacity-25">
                                            <span class="text-white-50 small d-block mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">VÁLIDA HASTA EL</span>
                                            <span class="fs-5 fw-bold text-white"><?= date('d/m/Y', strtotime($licInfo['fecha_fin'])) ?></span>
                                        </div>
                                        <div>
                                            <span class="text-white-50 small d-block mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">TIEMPO RESTANTE</span>
                                            <span class="fs-5 fw-bold" style="color: <?= $barColor ?>;">Te quedan <?= max(0, $diasRestantes) ?> días</span>
                                        </div>
                                    </div>
                                    <?php elseif ($esPerpetua): ?>
                                    <div class="d-inline-flex align-items-center bg-dark bg-opacity-25 px-4 py-3 rounded-3 mt-2 border border-secondary border-opacity-25 shadow-sm">
                                        <div>
                                            <span class="text-white-50 small d-block mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">TIPO DE VIGENCIA</span>
                                            <span class="fs-5 fw-bold text-info"><i class="bi bi-infinity me-1"></i> Licencia Perpetua (Sin Vencimiento)</span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="lic-bar-track mt-3">
                                <div class="lic-bar-fill" id="licBar"
                                     style="width:0%;background:<?= $barColor ?>; box-shadow: 0 0 10px <?= $barColor ?>;"></div>
                            </div>
                        </div>

                        <!-- Detalles -->
                        <div class="card-body p-3">
                            <?php if ($licInfo): ?>
                            <div class="row g-2">
                                <div class="col-6 col-md-4">
                                    <div class="info-box">
                                        <div class="lbl"><i class="bi bi-calendar-check me-1"></i>Vence el</div>
                                        <div class="val">
                                            <?= isset($licInfo['fecha_fin'])
                                                ? date('d/m/Y', strtotime($licInfo['fecha_fin']))
                                                : 'Perpetua' ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="info-box">
                                        <div class="lbl"><i class="bi bi-hourglass-split me-1"></i>Días restantes</div>
                                        <div class="val" style="color:<?= $barColor ?>;">
                                            <?= $diasRestantes !== null ? max(0, $diasRestantes) . ' días' : '∞' ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="info-box">
                                        <div class="lbl"><i class="bi bi-award me-1"></i>Tipo de Licencia</div>
                                        <div class="val text-primary"><?= htmlspecialchars($licInfo['tipo'] ?? 'N/A') ?></div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="info-box">
                                        <div class="lbl"><i class="bi bi-building me-1"></i>RUC / NIT</div>
                                        <div class="val"><?= htmlspecialchars($licInfo['ruc'] ?? 'N/A') ?></div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="info-box">
                                        <div class="lbl"><i class="bi bi-person-badge me-1"></i>Cliente / ID</div>
                                        <div class="val"><?= htmlspecialchars($licInfo['empresa_id'] ?? 'N/A') ?></div>
                                    </div>
                                </div>
                                <?php if (!empty($licInfo['emision'])): ?>
                                <div class="col-6 col-md-4">
                                    <div class="info-box">
                                        <div class="lbl"><i class="bi bi-calendar-plus me-1"></i>Fecha de emisión</div>
                                        <div class="val"><?= date('d/m/Y', (int)$licInfo['emision']) ?></div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-exclamation-triangle-fill fs-1 text-warning d-block mb-2"></i>
                                No se encontró ninguna licencia instalada.<br>
                                <small>Use el formulario de la derecha para activar una.</small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ── Cargar / Renovar Licencia ── -->
                <div class="col-12 col-xl-5">
                    <div class="card shadow-sm border-0 token-card h-100" style="border-radius:12px;">
                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="fw-bold mb-1">
                                <i class="bi bi-key-fill me-2 text-primary"></i>
                                <?= $licValida ? 'Renovar / Cargar Licencia JWT' : 'Activar licencia' ?>
                            </h5>
                            <p class="text-muted small mb-3">
                                Pegue aquí el token JWT proporcionado por su proveedor. Se verificará de forma local y remota.
                            </p>

                            <form action="<?= BASE_URL ?>/license/activar" method="POST"
                                  class="d-flex flex-column flex-grow-1">
                                <input type="hidden" name="from" value="panel">
                                <div class="mb-3 flex-grow-1 d-flex flex-column">
                                    <label for="token_input" class="form-label fw-semibold small">
                                        Token JWT
                                    </label>
                                    <textarea name="token" id="token_input"
                                        class="form-control flex-grow-1" rows="7"
                                        placeholder="Pegue su token de licencia JWT aquí…&#10;&#10;Ejemplo:&#10;eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
                                        required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">
                                    <i class="bi bi-shield-fill-check me-2"></i>
                                    <?= $licValida ? 'Aplicar nueva licencia' : 'Activar sistema' ?>
                                </button>
                            </form>

                            <div class="alert alert-info d-flex gap-2 align-items-start mt-3 mb-0 py-2 px-3"
                                 style="font-size:.82rem;">
                                <i class="bi bi-info-circle-fill mt-1" style="flex-shrink:0;"></i>
                                <span>Operación <strong>Dual (Offline/Online)</strong>. Se valida localmente sin internet y revalida de fondo con el servidor central.</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /row -->
        </div><!-- /container -->
    </div><!-- /main-content-area -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('load', function () {
            var bar = document.getElementById('licBar');
            if (bar) {
                setTimeout(function () {
                    bar.style.width = '<?= $porcentaje ?>%';
                }, 300);
            }
        });
    </script>
</body>
</html>
