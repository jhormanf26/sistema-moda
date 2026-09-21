<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Gastos - Sistema Moda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <style>
        /* FIX DE LAYOUT CRÍTICO: Igual que en Inventario e Historial */
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
            <h2 class="fw-bold m-0" style="color: var(--text-dark);"><i class="bi bi-cash-coin me-2" style="color: var(--primary);"></i> Gastos / Salidas</h2>
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>/gasto/exportarExcel<?= isset($_GET['fecha_inicio']) ? '?fecha_inicio='.$_GET['fecha_inicio'].'&fecha_fin='.$_GET['fecha_fin'] : '' ?>" class="btn btn-success rounded-pill px-4 shadow-sm" target="_blank">
                    <i class="bi bi-file-earmark-excel me-1"></i> Exportar a Excel
                </a>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalGasto">
                    <i class="bi bi-plus-circle me-1"></i> Registrar Gasto
                </button>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-info alert-dismissible fade show"><i class="bi bi-check-circle-fill"></i> Gasto registrado correctamente.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <!-- Filtro por fechas -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form method="GET" action="<?= BASE_URL ?>/gasto/index" class="row gx-3 gy-2 align-items-end">
                    <div class="col-sm-4 col-md-3">
                        <label class="form-label small text-muted text-uppercase fw-bold mb-1"><i class="bi bi-calendar-event me-1"></i> Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? date('Y-m-d')) ?>" required>
                    </div>
                    <div class="col-sm-4 col-md-3">
                        <label class="form-label small text-muted text-uppercase fw-bold mb-1"><i class="bi bi-calendar-event me-1"></i> Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($_GET['fecha_fin'] ?? date('Y-m-d')) ?>" required>
                    </div>
                    <div class="col-sm-4 col-md-auto d-flex gap-2">
                        <button type="submit" class="btn btn-secondary shadow-sm px-3 fw-bold">
                            <i class="bi bi-search me-1"></i> Filtrar
                        </button>
                        <?php if(isset($_GET['fecha_inicio'])): ?>
                            <a href="<?= BASE_URL ?>/gasto/index" class="btn btn-outline-danger shadow-sm px-3 fw-bold" title="Limpiar y ver turno actual">
                                <i class="bi bi-x-circle me-1"></i> Limpiar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-5 col-lg-4">
                <div class="widget-card text-center p-4">
                    <div class="widget-icon bg-opacity-10 text-primary mx-auto mb-3" style="background-color: var(--accent); width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 1.5rem;">
                        <i class="bi bi-graph-down-arrow"></i>
                    </div>
                    <div class="text-muted small text-uppercase fw-bold tracking-wide mb-1">
                        <?= isset($_GET['fecha_inicio']) ? 'Total Filtrado' : 'Total Gastos Hoy' ?>
                    </div>
                    <h2 class="display-5 fw-bold mb-0" style="color: var(--primary);"><span class="fs-4 text-muted"><?= htmlspecialchars($monedaEmpresa ?? '$') ?></span> <?= formatearCOP($totalHoy ?? 0) ?></h2>
                </div>
            </div>
        </div>

        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title m-0 fw-bold" style="color: var(--text-dark);">
                    <?php if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])): ?>
                        Gastos Registrados (<?= date('d/m/Y', strtotime($_GET['fecha_inicio'])) ?> - <?= date('d/m/Y', strtotime($_GET['fecha_fin'])) ?>)
                    <?php else: ?>
                        Gastos Registrados en este Turno
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Fecha/Hora</th>
                                <th>Monto</th>
                                <th>Motivo / Descripción</th>
                                <th>Registrado Por</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($gastos)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <h4><?php echo isset($_GET['fecha_inicio']) ? 'No hay gastos registrados en este rango de fechas.' : 'No hay gastos registrados en este turno.'; ?></h4>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($gastos as $g): ?>
                                <tr>
                                    <td class="ps-4 text-muted">#<?= str_pad($g['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($g['fecha'])) ?></td>
                                    <td class="fw-bold" style="color: var(--primary);">- <?= htmlspecialchars($monedaEmpresa ?? '$') ?> <?= formatearCOP($g['monto']) ?></td>
                                    <td><?= htmlspecialchars($g['descripcion']) ?></td>
                                    <td><span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-person me-1 text-muted"></i> <?= htmlspecialchars($g['usuario_nombre']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modalGasto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-dash-circle me-2"></i> Registrar Salida de Dinero</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>/gasto/guardar" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label for="monto" class="form-label fw-bold small text-muted text-uppercase tracking-wide">Monto a Retirar (<?= htmlspecialchars($monedaEmpresa ?? '$') ?>)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-end-0 text-muted fw-bold"><?= htmlspecialchars($monedaEmpresa ?? '$') ?></span>
                            <input type="number" step="1" min="1" class="form-control border-start-0 fw-bold fs-3" style="color: var(--primary);" id="monto" name="monto" required placeholder="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-bold small text-muted text-uppercase tracking-wide">Motivo / Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required placeholder="Ej: Pago de movilidad, útiles..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn text-muted fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Guardar Gasto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>