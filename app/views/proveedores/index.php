<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Proveedores - Sistema Moda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* FIX DE LAYOUT CRÍTICO: Asegura que el contenido se mueva a la derecha del sidebar fijo */
        @media (min-width: 992px) {
            .offcanvas-lg { position: fixed !important; top: 0; }
            .main-content-area { 
                margin-left: 260px; /* Ancho del sidebar */
                width: calc(100% - 260px);
            }
        }
        @media (max-width: 767px) { .offcanvas-open { overflow: hidden !important; } }
    </style>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
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
            <h2 class="fw-bold m-0"><i class="bi bi-truck"></i> Gestión de Proveedores</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProveedor" onclick="prepararModal()">
                <i class="bi bi-plus-lg"></i> Nuevo Proveedor
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
                                <th>Razón Social</th>
                                <th>RUC</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Verificamos si hay proveedores
                            if(empty($proveedores)): ?>
                                <tr><td colspan="6" class="text-center py-4"><h4>No hay proveedores registrados.</h4></td></tr>
                            <?php else: ?>
                                <?php foreach($proveedores as $p): ?>
                                <tr>
                                    <td><?= $p['id'] ?></td>
                                    <td class="fw-bold"><?= $p['razon_social'] ?></td>
                                    <td><span class="badge bg-secondary"><?= $p['ruc'] ?></span></td>
                                    <td><?= $p['telefono'] ?? '-' ?></td>
                                    <td><?= $p['email'] ?? '-' ?></td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-warning text-dark shadow-sm" 
                                            onclick="editarProveedor(<?= $p['id'] ?>, '<?= $p['razon_social'] ?>', '<?= $p['ruc'] ?>', '<?= $p['telefono'] ?>', '<?= $p['correo'] ?>', '<?= $p['direccion'] ?>')">
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

<div class="modal fade" id="modalProveedor" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Nuevo Proveedor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formProveedor" action="<?= BASE_URL ?>/proveedor/guardar" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="proveedorId" name="id">
                    
                    <div class="mb-3">
                        <label class="form-label">Razón Social *</label>
                        <input type="text" class="form-control" id="razon_social" name="razon_social" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">RUC *</label>
                            <input type="text" class="form-control" id="ruc" name="ruc" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar Proveedor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modalProveedor = new bootstrap.Modal(document.getElementById('modalProveedor'));
    const form = document.getElementById('formProveedor');
    const BASE_URL = '<?= BASE_URL ?>';

    function prepararModal() {
        document.getElementById('modalTitle').innerText = 'Nuevo Proveedor';
        document.getElementById('btnGuardar').innerText = 'Guardar Proveedor';
        form.action = '<?= BASE_URL ?>/proveedor/guardar';
        document.getElementById('proveedorId').value = '';
        form.reset();
    }

    function editarProveedor(id, razon_social, ruc, telefono, correo, direccion) {
        document.getElementById('modalTitle').innerText = 'Editar Proveedor';
        document.getElementById('btnGuardar').innerText = 'Actualizar Proveedor';
        form.action = '<?= BASE_URL ?>/proveedor/guardar'; // El modelo actualizará si recibe ID

        document.getElementById('proveedorId').value = id;
        document.getElementById('razon_social').value = razon_social;
        document.getElementById('ruc').value = ruc;
        document.getElementById('telefono').value = (telefono === 'null') ? '' : telefono;
        document.getElementById('correo').value = (correo === 'null') ? '' : correo;
        document.getElementById('direccion').value = (direccion === 'null') ? '' : direccion;
        
        modalProveedor.show();
    }
</script>

</body>
</html>
