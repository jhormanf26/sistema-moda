<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Clientes - Sistema Moda</title>
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
            <h2 class="fw-bold m-0"><i class="bi bi-people"></i> Gestión de Clientes</h2>
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>/cliente/exportarExcel<?= !empty($_GET['buscar']) ? '?buscar='.urlencode($_GET['buscar']) : '' ?>" class="btn btn-success shadow-sm" target="_blank">
                    <i class="bi bi-file-earmark-excel me-1"></i> Exportar
                </a>
                <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCliente" onclick="prepararModal()">
                    <i class="bi bi-person-plus-fill me-1"></i> Nuevo Cliente
                </button>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-info alert-dismissible fade show"><i class="bi bi-check-circle-fill"></i> Acción realizada correctamente.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <!-- Formulario de Búsqueda -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form method="GET" action="<?= BASE_URL ?>/cliente/index" class="row gx-2 gy-2 align-items-center">
                    <div class="col-md-6 col-lg-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="buscar" class="form-control border-start-0 ps-0" placeholder="Buscar por nombre o documento..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-auto d-flex gap-2">
                        <button type="submit" class="btn btn-secondary shadow-sm px-3 fw-bold">Buscar</button>
                        <?php if(!empty($_GET['buscar'])): ?>
                            <a href="<?= BASE_URL ?>/cliente/index" class="btn btn-outline-danger shadow-sm px-3 fw-bold">Limpiar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre / Razón Social</th>
                                <th>Documento</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Verificamos que existan clientes
                            if(empty($clientes)): ?>
                                <tr><td colspan="6" class="text-center py-4"><h4>No hay clientes registrados.</h4></td></tr>
                            <?php else: ?>
                                <?php foreach($clientes as $c): ?>
                                <tr>
                                    <td><?= $c['id'] ?></td>
                                    <td class="fw-bold"><?= $c['nombre'] ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= $c['documento'] ?></span></td>
                                    <td><?= $c['telefono'] ?? '-' ?></td>
                                    <td><?= $c['correo'] ?? '-' ?></td>
                                    <td class="text-end pe-4">
                                        <?php if($c['id'] != 1): // El ID 1 suele ser Público General y no se edita ?>
                                        <button class="btn btn-sm btn-warning text-dark shadow-sm" 
                                                onclick="editarCliente(<?= $c['id'] ?>, '<?= $c['nombre'] ?>', '<?= $c['documento'] ?>', '<?= $c['telefono'] ?>', '<?= $c['correo'] ?>', '<?= $c['direccion'] ?>')">
                                            <i class="bi bi-pencil-fill"></i> Editar
                                        </button>
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
</div>

<div class="modal fade" id="modalCliente" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Nuevo Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCliente" action="<?= BASE_URL ?>/cliente/guardar" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="clienteId" name="id">
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Nombre / Razón Social *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">DNI / RUC</label>
                            <input type="text" class="form-control" id="documento" name="documento">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo</label>
                            <input type="email" class="form-control" id="correo" name="correo">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modalCliente = new bootstrap.Modal(document.getElementById('modalCliente'));
    const form = document.getElementById('formCliente');
    const BASE_URL = '<?= BASE_URL ?>';

    function prepararModal() {
        document.getElementById('modalTitle').innerText = 'Nuevo Cliente';
        document.getElementById('btnGuardar').innerText = 'Guardar Cliente';
        form.action = '<?= BASE_URL ?>/cliente/guardar';
        document.getElementById('clienteId').value = '';
        form.reset();
    }

    function editarCliente(id, nombre, documento, telefono, correo, direccion) {
        document.getElementById('modalTitle').innerText = 'Editar Cliente';
        document.getElementById('btnGuardar').innerText = 'Actualizar Cliente';
        
        // Llenar campos
        document.getElementById('clienteId').value = id;
        document.getElementById('nombre').value = nombre;
        document.getElementById('documento').value = documento;
        document.getElementById('telefono').value = (telefono === 'null') ? '' : telefono;
        document.getElementById('correo').value = (correo === 'null') ? '' : correo;
        document.getElementById('direccion').value = (direccion === 'null') ? '' : direccion;
        
        modalCliente.show();
    }
</script>

</body>
</html>
