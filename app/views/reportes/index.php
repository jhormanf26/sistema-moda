<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reportes - Sistema Moda</title>
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>

<body>

    <nav class="navbar navbar-dark bg-dark d-lg-none shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold">Sistema Moda</span>
            <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                <i class="bi bi-list"></i> Menú
            </button>
        </div>
    </nav>

    <?php include '../app/views/layouts/sidebar.php'; ?>

    <div class="main-content-area" style="overflow-y: auto; height: 100vh;">
        <div class="container-fluid px-0">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title">Centro de Reportes</h2>
                    <p class="page-subtitle mb-0">Genera y visualiza la información clave de tu boutique.</p>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <!-- Tarjetas de ejemplos de reportes futuros -->
                <div class="col-12 col-md-4">
                    <div class="glass-card p-4 h-100 d-flex flex-column justify-content-center text-center">
                        <i class="bi bi-file-earmark-bar-graph text-primary mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title-modern">Reporte de Ventas</h5>
                        <p class="text-muted small mt-2">Detalle de ingresos por periodos, clientes y sucursales.</p>
                        <button class="btn btn-outline-primary mt-3 w-100 rounded-pill">Generar Reporte</button>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="glass-card p-4 h-100 d-flex flex-column justify-content-center text-center">
                        <i class="bi bi-box-seam text-secondary mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title-modern">Rotación de Inventario</h5>
                        <p class="text-muted small mt-2">Productos más vendidos, estancados y alertas de reposición.</p>
                        <button class="btn btn-outline-secondary mt-3 w-100 rounded-pill">Generar Reporte</button>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="glass-card p-4 h-100 d-flex flex-column justify-content-center text-center">
                        <i class="bi bi-cash-coin text-success mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title-modern">Flujo de Caja</h5>
                        <p class="text-muted small mt-2">Resumen de ingresos vs gastos, utilidad neta proyectada.</p>
                        <button class="btn btn-outline-success mt-3 w-100 rounded-pill">Generar Reporte</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>