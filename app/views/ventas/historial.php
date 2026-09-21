<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Ventas - Sistema Moda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <style>
        /* SOLUCIÓN DE LAYOUT ROBUSTA (Igual que en Inventario) */
        /* En pantallas grandes, empujamos todo el cuerpo a la derecha */
        @media (min-width: 992px) {
            body {
                padding-left: 260px;
            }

            .navbar-mobile {
                display: none !important;
            }
        }

        /* En móviles, quitamos el padding y mostramos el menú hamburguesa */
        @media (max-width: 991px) {
            body {
                padding-left: 0;
            }

            .navbar-mobile {
                display: flex !important;
            }
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark navbar-mobile shadow-sm sticky-top">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold">Sistema Moda</span>
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </nav>

    <?php include '../app/views/layouts/sidebar.php'; ?>

    <div class="container-fluid p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold m-0"><i class="bi bi-clock-history"></i> Historial de Ventas</h2>
            <div>
                <a href="<?= BASE_URL ?>/ventas" class="btn btn-outline-dark me-2"><i class="bi bi-shop"></i> TPV</a>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4 bg-white">
            <div class="card-body py-3">
                <form method="GET" action="<?= BASE_URL ?>/ventas/historial" class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Fecha Inicio</label>
                        <input type="date" class="form-control form-control-sm" name="fecha_inicio" value="<?= $_GET['fecha_inicio'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Fecha Fin</label>
                        <input type="date" class="form-control form-control-sm" name="fecha_fin" value="<?= $_GET['fecha_fin'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Buscar Cliente/Vendedor/Ticket</label>
                        <input type="text" class="form-control form-control-sm" name="busqueda" placeholder="Ej: Juan, #12" value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end mt-4 mt-md-0">
                        <button type="submit" class="btn btn-primary btn-sm me-2 w-100"><i class="bi bi-search"></i> Filtrar</button>
                        <a href="<?= BASE_URL ?>/ventas/historial" class="btn btn-outline-secondary btn-sm w-100 me-2" title="Limpiar Filtros"><i class="bi bi-eraser"></i></a>
                        
                        <?php 
                        $params = "";
                        if(isset($_GET['fecha_inicio']) || isset($_GET['fecha_fin']) || isset($_GET['busqueda'])){
                            $params = "?fecha_inicio=" . ($_GET['fecha_inicio'] ?? '') . "&fecha_fin=" . ($_GET['fecha_fin'] ?? '') . "&busqueda=" . urlencode($_GET['busqueda'] ?? '');
                        }
                        ?>
                        <a href="<?= BASE_URL ?>/ventas/exportar<?= $params ?>" class="btn btn-success btn-sm w-100"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                    </div>
                </form>
            </div>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'canceled'): ?>
            <div class="alert alert-warning alert-dismissible fade show"><i class="bi bi-exclamation-triangle-fill"></i>
                Venta anulada correctamente.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-octagon-fill"></i> Error: No se
                pudo anular la venta o no tienes permiso.<button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Ticket</th>
                                <th>Fecha/Hora</th>
                                <th>Total</th>
                                <th>Cliente</th>
                                <th>Vendedor</th>
                                <th>F. Pago</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ventas)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <h4>No hay ventas registradas.</h4>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ventas as $v): ?>
                                    <tr class="<?= $v['estado'] == 'anulada' ? 'table-danger text-muted' : '' ?>">
                                        <td class="ps-4">#<?= str_pad($v['id'], 6, '0', STR_PAD_LEFT) ?></td>
                                        <td><?= $v['fecha'] ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($monedaEmpresa ?? '$') ?>
                                            <?= formatearCOP($v['total']) ?></td>
                                        <td><?= $v['cliente_nombre'] ?? 'Público General' ?></td>
                                        <td><?= $v['vendedor'] ?></td>
                                        <td>
                                            <?php 
                                            $fpClass = 'bg-secondary';
                                            if($v['forma_pago'] == 'efectivo') $fpClass = 'bg-success';
                                            elseif($v['forma_pago'] == 'yape' || $v['forma_pago'] == 'plin') $fpClass = 'bg-info text-dark';
                                            elseif($v['forma_pago'] == 'tarjeta') $fpClass = 'bg-primary';
                                            ?>
                                            <span class="badge <?= $fpClass ?>"><?= strtoupper($v['forma_pago']) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($v['estado'] == 'completada'): ?>
                                                <span class="badge bg-success">Completada</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Anulada</span>
                                                <small class="d-block text-muted">Anuló: <?= $v['quien_anulo'] ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="<?= BASE_URL ?>/ventas/imprimir/<?= $v['id'] ?>" target="_blank"
                                                class="btn btn-sm btn-info text-white me-2" title="Imprimir Ticket"><i
                                                    class="bi bi-printer-fill"></i></a>

                                            <?php if ($v['estado'] == 'completada' && isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 'admin'): ?>
                                                <button class="btn btn-sm btn-danger" onclick="confirmarAnulacion(<?= $v['id'] ?>)"
                                                    title="Anular Venta"><i class="bi bi-x-circle-fill"></i></button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="modalAnular" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-octagon-fill"></i> Confirmar Anulación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="formAnular" method="GET">
                    <div class="modal-body">
                        <p>Esta acción es irreversible y requiere un motivo. El stock será repuesto.</p>
                        <div class="mb-3">
                            <label for="motivoAnulacion" class="form-label">Motivo de la Anulación (Obligatorio)</label>
                            <textarea class="form-control" id="motivoAnulacion" name="motivo" rows="3"
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Anular Venta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const BASE_URL = '<?= BASE_URL ?>';
        const modalAnular = new bootstrap.Modal(document.getElementById('modalAnular'));

        function confirmarAnulacion(ventaId) {
            const form = document.getElementById('formAnular');
            form.action = `${BASE_URL}/ventas/anular/${ventaId}`;
            modalAnular.show();
        }
    </script>

</body>

</html>