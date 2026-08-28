<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario - Sistema Moda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* SOLUCIÓN DE LAYOUT ROBUSTA */
        /* En pantallas grandes, empujamos todo el cuerpo a la derecha */
        @media (min-width: 992px) {
            body { padding-left: 260px; }
            .navbar-mobile { display: none !important; }
        }
        /* En móviles, quitamos el padding y mostramos el menú hamburguesa */
        @media (max-width: 991px) {
            body { padding-left: 0; }
            .navbar-mobile { display: flex !important; }
        }
    </style>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
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
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0"><i class="bi bi-box-seam"></i> Inventario</h2>
            <p class="text-muted">Gestión de Productos</p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/ventas" class="btn btn-outline-dark me-2"><i class="bi bi-shop"></i> Ir a Vender</a>
            <a href="<?= BASE_URL ?>/producto/exportar" class="btn btn-success me-2"><i class="bi bi-file-earmark-excel"></i> Excel</a>
            
            <?php if(isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 'admin'): ?>
                <a href="<?= BASE_URL ?>/producto/crear" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nuevo Producto</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <?php if($_GET['msg'] == 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle"></i> Producto registrado.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php elseif($_GET['msg'] == 'updated'): ?>
            <div class="alert alert-warning alert-dismissible fade show"><i class="bi bi-pencil"></i> Producto actualizado.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php elseif($_GET['msg'] == 'status_changed'): ?>
            <div class="alert alert-info alert-dismissible fade show"><i class="bi bi-arrow-repeat"></i> Estado actualizado.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Producto</th>
                            <th>Categoría</th>
                            <th>Precio Venta</th>
                            <th>Stock Total</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($productos)): ?>
                            <tr><td colspan="6" class="text-center py-5"><h4>📭 Inventario vacío</h4></td></tr>
                        <?php else: ?>
                            <?php foreach($productos as $p): ?>
                            <tr class="<?= $p['activo'] == 0 ? 'table-secondary text-muted' : '' ?>">
                                <td class="ps-4">
                                    <div class="fw-bold"><?= $p['nombre'] ?></div>
                                    <small class="text-muted">ID: <?= str_pad($p['id'], 4, '0', STR_PAD_LEFT) ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= $p['categoria_nombre'] ?? 'General' ?></span></td>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($monedaEmpresa ?? 'S/') ?> <?= number_format($p['precio_venta'], 2) ?></td>
                                <td>
                                    <?php if($p['stock_total'] < 5): ?>
                                        <span class="badge bg-danger rounded-pill"><?= $p['stock_total'] ?> u.</span>
                                    <?php else: ?>
                                        <span class="badge bg-success rounded-pill"><?= $p['stock_total'] ?> u.</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($p['activo'] == 1): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-info text-white shadow-sm me-1" 
                                            onclick="verVariantes(<?= $p['id'] ?>, '<?= $p['nombre'] ?>')" title="Ver Stock">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>

                                    <button class="btn btn-sm btn-secondary shadow-sm me-1" 
                                            onclick="verKardex(<?= $p['id'] ?>, '<?= $p['nombre'] ?>')" title="Ver Movimientos">
                                        <i class="bi bi-clipboard-data"></i>
                                    </button>

                                    <?php if(isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 'admin'): ?>
                                        <a href="<?= BASE_URL ?>/producto/editar/<?= $p['id'] ?>" 
                                           class="btn btn-sm btn-warning text-dark shadow-sm me-1" title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <?php if($p['activo'] == 1): ?>
                                            <a href="<?= BASE_URL ?>/producto/cambiarEstado/<?= $p['id'] ?>/1" 
                                               class="btn btn-sm btn-success shadow-sm" title="Desactivar">
                                                <i class="bi bi-toggle-on"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= BASE_URL ?>/producto/cambiarEstado/<?= $p['id'] ?>/0" 
                                               class="btn btn-sm btn-secondary shadow-sm" title="Activar">
                                                <i class="bi bi-toggle-off"></i>
                                            </a>
                                        <?php endif; ?>
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

<div class="modal fade" id="modalVariantes" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Variantes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-striped mb-0 text-center">
                    <thead class="table-light"><tr><th>Talla</th><th>Color</th><th>Stock</th></tr></thead>
                    <tbody id="cuerpoTablaVariantes"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKardex" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-clipboard-data"></i> Trazabilidad: <span id="tituloKardex"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-sm table-striped mb-0" style="font-size: 0.85rem;">
                    <thead class="table-light">
                        <tr><th>Fecha</th><th>Usuario</th><th>Variante</th><th>Tipo</th><th>Cant.</th><th>Motivo / Ref.</th></tr>
                    </thead>
                    <tbody id="cuerpoKardex"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const BASE_URL = '<?= BASE_URL ?>';
    const modalVariantes = new bootstrap.Modal(document.getElementById('modalVariantes'));
    const modalKardex = new bootstrap.Modal(document.getElementById('modalKardex'));

    function verVariantes(id, nombre) {
        document.querySelector('#modalVariantes .modal-title').innerText = 'Variantes: ' + nombre;
        const tbody = document.getElementById('cuerpoTablaVariantes'); 
        tbody.innerHTML = '<tr><td colspan="3">Cargando...</td></tr>'; 
        modalVariantes.show();
        
        fetch(`${BASE_URL}/producto/obtenerVariantes/${id}`)
            .then(r => r.json())
            .then(d => {
                tbody.innerHTML = ''; 
                d.forEach(v => { tbody.innerHTML += `<tr><td>${v.talla}</td><td>${v.color}</td><td>${v.stock_actual}</td></tr>`; });
            });
    }

    function verKardex(id, nombre) {
        document.getElementById('tituloKardex').innerText = nombre;
        const tbody = document.getElementById('cuerpoKardex'); 
        tbody.innerHTML = '<tr><td colspan="6" class="text-center p-3">Cargando historial...</td></tr>'; 
        modalKardex.show();
        
        fetch(`${BASE_URL}/producto/historial/${id}`)
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if(data.length === 0) { tbody.innerHTML = '<tr><td colspan="6" class="text-center p-3 text-muted">Sin movimientos registrados.</td></tr>'; return; }
                
                data.forEach(m => {
                    let color = m.tipo === 'entrada' ? 'text-success' : 'text-danger';
                    let signo = m.tipo === 'entrada' ? '+' : '-';
                    
                    tbody.innerHTML += `
                        <tr>
                            <td>${m.fecha.substring(0, 16)}</td>
                            <td>${m.usuario || '-'}</td>
                            <td><span class="badge bg-light text-dark border">${m.talla}/${m.color}</span></td>
                            <td class="${color} fw-bold text-uppercase">${m.tipo}</td>
                            <td class="fw-bold">${signo} ${m.cantidad}</td>
                            <td>${m.descripcion}</td>
                        </tr>`;
                });
            })
            .catch(error => {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center p-3 text-danger">Error al cargar el Kardex.</td></tr>';
            });
    }
</script>
</body>
</html>
