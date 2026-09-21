<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cierre de Caja - Sistema Moda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <style>
        /* FIX DE LAYOUT CRÍTICO: Igual que en los otros módulos */
        @media (min-width: 992px) {
            .offcanvas-lg { position: fixed !important; top: 0; }
            .main-content-area { 
                margin-left: 260px; /* Ancho del sidebar */
                width: calc(100% - 260px);
            }
        }
        @media (max-width: 767px) { .offcanvas-open { overflow: hidden !important; } }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark d-lg-none shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">Sistema Moda</span>
        <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
            <i class="bi bi-list"></i> Menú
        </button>
    </div>
</nav>

<?php include '../app/views/layouts/sidebar.php'; ?>

<div class="main-content-area" style="height: 100vh; overflow-y: auto;">
    <div class="container-fluid p-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0" style="color: var(--text-dark);"><i class="bi bi-wallet2 me-2" style="color: var(--primary);"></i> Cierre de Caja</h2>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Tarjeta Principal del Cierre -->
                <div class="card card-custom border-0 shadow-sm p-4">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex justify-content-center align-items-center rounded-circle mb-3 shadow-sm" style="width: 70px; height: 70px; background-color: rgba(201,132,122,0.15); color: var(--primary); font-size: 2rem;">
                            <i class="bi bi-lock-fill"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">Cierre de Turno</h4>
                        <p class="text-muted small text-uppercase tracking-wide">Resumen Financiero</p>
                    </div>

                    <div class="bg-light rounded-3 p-4 mb-4 border" style="border-color: var(--border-color) !important;">
                    
                    <div class="d-flex justify-content-between mb-1 border-bottom pb-1">
                        <span class="text-muted">Monto Apertura (Base):</span>
                        <span class="fw-bold"><?= htmlspecialchars($monedaEmpresa ?? '$') ?> <?= formatearCOP($caja['monto_apertura']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 border-bottom pb-1">
                        <span class="text-muted">Ventas Realizadas (+):</span>
                        <span class="fw-bold text-success"><?= htmlspecialchars($monedaEmpresa ?? '$') ?> <?= formatearCOP($ventas_sesion) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 pb-2">
                        <span class="text-muted fw-bold small text-uppercase tracking-wide">Gastos / Salidas (-):</span>
                        <span class="fw-bold text-danger"><?= htmlspecialchars($monedaEmpresa ?? '$') ?> <?= formatearCOP($gastos_sesion) ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top" style="border-top-color: var(--border-color) !important;">
                        <span class="fw-bold text-dark text-uppercase tracking-wide">Total Esperado:</span>
                        <span class="fs-4 fw-bold" style="color: var(--primary);">
                            <?= htmlspecialchars($monedaEmpresa ?? '$') ?> <?= formatearCOP(($caja['monto_apertura'] + $ventas_sesion) - $gastos_sesion) ?>
                        </span>
                    </div>
                </div>

                <form action="<?= BASE_URL ?>/caja/cerrar" method="POST">
                    <input type="hidden" name="id_sesion" value="<?= $caja['id'] ?>">
                    <input type="hidden" name="total_ventas" value="<?= $ventas_sesion ?>">
                    <input type="hidden" name="total_gastos" value="<?= $gastos_sesion ?>">

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase tracking-wide">¿Cuánto dinero hay FÍSICAMENTE en el cajón?</label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted fw-bold">💰 <?= htmlspecialchars($monedaEmpresa ?? '$') ?></span>
                            <input type="number" step="1" name="monto_fisico" class="form-control border-start-0 fw-bold fs-3" style="color: var(--primary);" required placeholder="0">
                        </div>
                        <div class="form-text text-center mt-3 text-muted">Ingresa el monto real contado. El sistema calculará automáticamente si sobra o falta dinero.</div>
                    </div>

                    <div class="d-grid gap-3 mt-5">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm" style="letter-spacing: 1px;">
                            CONFIRMAR CIERRE DE CAJA <i class="bi bi-check2-circle ms-1"></i>
                        </button>
                        <a href="<?= BASE_URL ?>/ventas" class="btn btn-light rounded-pill text-muted fw-bold border">
                            <i class="bi bi-arrow-left"></i> Cancelar y volver a vender
                        </a>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>