<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Sucursales - Sistema Moda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* FIX DE LAYOUT CRÍTICO: Igual que en Inventario */
        @media (min-width: 992px) {
            .offcanvas-lg { position: fixed !important; top: 0; }
            .main-content-area { 
                margin-left: 260px; /* Ancho del sidebar */
                width: calc(100% - 260px);
            }
            .navbar-mobile { display: none !important; }
        }
        @media (max-width: 991px) {
            .navbar-mobile { display: flex !important; }
        }
        @media (max-width: 767px) { .offcanvas-open { overflow: hidden !important; } }
    </style>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark navbar-mobile shadow-sm sticky-top">
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
            <h2 class="fw-bold m-0"><i class="bi bi-shop"></i> Gestión de Sucursales</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSucursal" onclick="prepararModal()">
                <i class="bi bi-plus-circle-fill"></i> Nueva Sucursal
            </button>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-info alert-dismissible fade show"><i class="bi bi-check-circle-fill"></i> Acción realizada correctamente.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(empty($sucursales)): ?>
                                <tr><td colspan="6" class="text-center py-4"><h4>No hay sucursales registradas.</h4></td></tr>
                            <?php else: ?>
                                <?php foreach($sucursales as $s): ?>
                                <tr>
                                    <td><?= $s['id'] ?></td>
                                    <td class="fw-bold"><?= $s['nombre'] ?></td>
                                    <td><?= $s['direccion'] ?? '-' ?></td>
                                    <td><?= $s['telefono'] ?? '-' ?></td>
                                    <td class="text-center">
                                        <?php if($s['activo'] == 1): ?>
                                            <span class="badge bg-success">Activa</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactiva</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-warning text-dark shadow-sm" 
                                                onclick="editarSucursal(<?= $s['id'] ?>, '<?= addslashes($s['nombre']) ?>', '<?= addslashes($s['direccion']) ?>', '<?= $s['telefono'] ?>', <?= $s['activo'] ?>)">
                                            <i class="bi bi-pencil-fill"></i> Editar
                                        </button>
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
</div>

<div class="modal fade" id="modalSucursal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Nueva Sucursal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSucursal" action="<?= BASE_URL ?>/sucursal/guardar" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="sucursalId" name="id">
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Sucursal *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono">
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="activo" name="activo" checked>
                        <label class="form-check-label" for="activo">Sucursal Activa</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar Sucursal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modalSucursal = new bootstrap.Modal(document.getElementById('modalSucursal'));
    const form = document.getElementById('formSucursal');

    function prepararModal() {
        document.getElementById('modalTitle').innerText = 'Nueva Sucursal';
        document.getElementById('btnGuardar').innerText = 'Guardar Sucursal';
        document.getElementById('sucursalId').value = '';
        document.getElementById('activo').checked = true;
        form.reset();
    }

    function editarSucursal(id, nombre, direccion, telefono, activo) {
        document.getElementById('modalTitle').innerText = 'Editar Sucursal';
        document.getElementById('btnGuardar').innerText = 'Actualizar Sucursal';
        
        document.getElementById('sucursalId').value = id;
        document.getElementById('nombre').value = nombre;
        document.getElementById('direccion').value = (direccion === 'null') ? '' : direccion;
        document.getElementById('telefono').value = (telefono === 'null') ? '' : telefono;
        document.getElementById('activo').checked = (activo == 1);
        
        modalSucursal.show();
    }
</script>

</body>
</html>

