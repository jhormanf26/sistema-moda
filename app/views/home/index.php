<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema Moda</title>
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                    <h2 class="page-title">Dashboard</h2>
                    <p class="page-subtitle mb-0">Un vistazo general al rendimiento de tu negocio hoy.</p>
                </div>
                <div>
                    <span class="badge bg-white text-dark border shadow-sm p-2 px-3 rounded-pill fw-medium">
                        <i class="bi bi-calendar3 text-primary me-2"></i> <?= date('d M, Y') ?>
                    </span>
                </div>
            </div>

            <!-- KPI Cards Row -->
            <div class="row g-4 mb-4">
                <!-- Ventas Hoy -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="kpi-card h-100" style="--kpi-color: var(--primary); --kpi-bg: rgba(201,132,122,0.12); --kpi-shadow: rgba(201,132,122,0.25);">
                        <div class="kpi-icon-wrap">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="kpi-data">
                            <h6 class="kpi-title">Ventas Hoy</h6>
                            <h2 class="kpi-value"><?= htmlspecialchars($monedaEmpresa ?? '$') ?> <?= formatearCOP($hoy['total'] ?? 0) ?></h2>
                            <p class="mb-0 mt-1 small text-muted"><i class="bi bi-receipt"></i> <?= $hoy['transacciones'] ?? 0 ?> confirmados</p>
                        </div>
                    </div>
                </div>

                <!-- Ventas Mes -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="kpi-card h-100" style="--kpi-color: var(--success); --kpi-bg: rgba(107,171,142,0.12); --kpi-shadow: rgba(107,171,142,0.25);">
                        <div class="kpi-icon-wrap">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div class="kpi-data">
                            <h6 class="kpi-title">Ingresos Mes</h6>
                            <h2 class="kpi-value"><?= htmlspecialchars($monedaEmpresa ?? '$') ?> <?= formatearCOP($mes['total'] ?? 0) ?></h2>
                            <p class="mb-0 mt-1 small text-muted"><i class="bi bi-cart"></i> <?= $mes['transacciones'] ?? 0 ?> tickets</p>
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="kpi-card h-100" style="--kpi-color: var(--secondary); --kpi-bg: rgba(155,127,166,0.12); --kpi-shadow: rgba(155,127,166,0.25);">
                        <div class="kpi-icon-wrap">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="kpi-data">
                            <h6 class="kpi-title">Catálogo</h6>
                            <h2 class="kpi-value"><?= number_format($totalProductos ?? 0) ?></h2>
                            <p class="mb-0 mt-1 small text-muted"><i class="bi bi-tags"></i> En toda la tienda</p>
                        </div>
                    </div>
                </div>

                <!-- Alertas Stock -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="kpi-card h-100" style="--kpi-color: var(--danger); --kpi-bg: rgba(207,102,121,0.12); --kpi-shadow: rgba(207,102,121,0.25);">
                        <div class="kpi-icon-wrap">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="kpi-data">
                            <h6 class="kpi-title">Alertas Stock</h6>
                            <h2 class="kpi-value"><?= number_format($alertasStock ?? 0) ?></h2>
                            <p class="mb-0 mt-1 small text-muted"><i class="bi bi-info-circle"></i> Reposición urgente</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-4">
                <!-- Line Chart -->
                <div class="col-12 col-lg-8">
                    <div class="card glass-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title-modern">Ingresos Recientes (7 días)</h5>
                            <button class="btn btn-sm btn-light border shadow-sm rounded-pill px-3"><i
                                    class="bi bi-download me-1"></i> Exportar</button>
                        </div>
                        <div class="card-body">
                            <div class="chart-container-line">
                                <canvas id="chartVentas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doughnut Chart -->
                <div class="col-12 col-lg-4">
                    <div class="card glass-card h-100">
                        <div class="card-header">
                            <h5 class="card-title-modern">Top Productos Vendidos</h5>
                        </div>
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <div class="chart-container-doughnut w-100">
                                <canvas id="chartProductos"></canvas>
                            </div>
                            <?php if (empty($topProductos)): ?>
                                <p class="text-muted mt-3 mb-0 text-center"><i class="bi bi-info-circle"></i> No hay datos
                                    de ventas recientes.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // 1. Recibir datos de PHP
        const historial = <?= $dataHistorial ?? '[]' ?>;
        const topProd = <?= $dataTop ?? '[]' ?>;

        // Configuración global de Chart.js para diseño moderno
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#7d879c';
        Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(43, 36, 49, 0.9)';
        Chart.defaults.plugins.tooltip.padding = 12;
        Chart.defaults.plugins.tooltip.cornerRadius = 8;
        Chart.defaults.plugins.tooltip.titleFont = { size: 14, weight: '600' };

        // 2. Configurar Gráfico de Líneas (Ventas)
        const ctxVentas = document.getElementById('chartVentas');
        if (ctxVentas && historial.length > 0) {
            // Crear gradiente para el área bajo la línea
            const ctx = ctxVentas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 350);
            gradient.addColorStop(0, 'rgba(201, 132, 122, 0.3)'); // primary boutique
            gradient.addColorStop(1, 'rgba(201, 132, 122, 0.0)');

            new Chart(ctxVentas, {
                type: 'line',
                data: {
                    labels: historial.map(item => {
                        const date = new Date(item.fecha + 'T00:00:00');
                        return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
                    }),
                    datasets: [{
                        label: 'Ingresos (' + G_MONEDA + ')',
                        data: historial.map(item => item.total),
                        borderColor: '#C9847A',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#C9847A',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [4, 4], color: 'rgba(0,0,0,0.05)', drawBorder: false },
                            ticks: { callback: function (value) { return G_MONEDA + ' ' + value; } }
                        },
                        x: {
                            grid: { display: false, drawBorder: false }
                        }
                    }
                }
            });
        }

        // 3. Configurar Gráfico de Torta (Productos)
        const ctxProd = document.getElementById('chartProductos');
        if (ctxProd && topProd.length > 0) {
            new Chart(ctxProd, {
                type: 'doughnut',
                data: {
                    labels: topProd.map(item => item.nombre),
                    datasets: [{
                        data: topProd.map(item => item.cantidad),
                        backgroundColor: [
                            '#C9847A', // Rose Gold
                            '#9B7FA6', // Mauve
                            '#E1B382', // Sand
                            '#6BAB8E', // Sage
                            '#D4A574'  // Gold
                        ],
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, padding: 20, boxWidth: 8 }
                        }
                    }
                }
            });
        }
    </script>

</body>

</html>
