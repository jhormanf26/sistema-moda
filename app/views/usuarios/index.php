<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - Sistema Moda</title>
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
            <h2 class="fw-bold m-0"><i class="bi bi-people-fill"></i> Gestión de Usuarios</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="prepararModal()">
                <i class="bi bi-person-plus-fill"></i> Nuevo Usuario
            </button>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-info alert-dismissible fade show"><i class="bi bi-check-circle-fill"></i> Acción realizada correctamente.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php elseif(isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-octagon-fill"></i> Error: No se pudo realizar la acción.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email (Usuario)</th>
                                <th>Sucursal</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Verificamos si hay usuarios
                            if(empty($usuarios)): ?>
                                <tr><td colspan="6" class="text-center py-4"><h4>No hay usuarios registrados.</h4></td></tr>
                            <?php else: ?>
                                <?php foreach($usuarios as $u): ?>
                                <tr class="<?= $u['activo'] == 0 ? 'table-secondary text-muted' : '' ?>">
                                    <td><?= $u['id'] ?></td>
                                    <td class="fw-bold"><?= $u['nombre'] ?></td>
                                    <td><?= $u['email'] ?></td>
                                    <td><span class="badge bg-secondary"><?= $u['sucursal_nombre'] ?? 'Principal' ?></span></td>
                                    <td>
                                        <span class="badge bg-<?= $u['rol'] == 'admin' ? 'danger' : 'success' ?> text-uppercase">
                                            <?= $u['rol'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($u['activo'] == 1): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-warning text-dark shadow-sm me-1" 
                                            onclick="editarUsuario(<?= $u['id'] ?>, '<?= $u['nombre'] ?>', '<?= $u['email'] ?>', '<?= $u['rol'] ?>', <?= $u['sucursal_id'] ?? 1 ?>)"
                                            title="Editar Usuario">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>

                                        <?php if($u['id'] != $_SESSION['user_id']): // No bloquearse a sí mismo ?>
                                            <?php if($u['activo'] == 1): ?>
                                                <a href="<?= BASE_URL ?>/usuario/cambiarEstado/<?= $u['id'] ?>/1" class="btn btn-sm btn-danger shadow-sm" title="Desactivar"><i class="bi bi-toggle-on"></i></a>
                                            <?php else: ?>
                                                <a href="<?= BASE_URL ?>/usuario/cambiarEstado/<?= $u['id'] ?>/0" class="btn btn-sm btn-secondary shadow-sm" title="Activar"><i class="bi bi-toggle-off"></i></a>
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
</div>

<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUsuario" action="<?= BASE_URL ?>/usuario/guardar" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="usuarioId" name="id">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label for="email" class="form-label">Email (Login)</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="vendedor">Vendedor</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sucursal_id" class="form-label">Sucursal</label>
                            <select class="form-select" id="sucursal_id" name="sucursal_id" required>
                                <?php foreach($sucursales_activas as $sa): ?>
                                    <option value="<?= $sa['id'] ?>"><?= $sa['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label" id="labelPassword">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text small text-muted" id="helpPassword"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modalUsuario = new bootstrap.Modal(document.getElementById('modalUsuario'));
    const form = document.getElementById('formUsuario');
    const labelPassword = document.getElementById('labelPassword');
    const inputPassword = document.getElementById('password');
    const helpPassword = document.getElementById('helpPassword');

    function prepararModal() {
        document.getElementById('modalTitle').innerText = 'Nuevo Usuario';
        document.getElementById('btnGuardar').innerText = 'Guardar Usuario';
        form.action = '<?= BASE_URL ?>/usuario/guardar';
        document.getElementById('usuarioId').value = '';
        
        // Resetear contraseña obligatoria
        inputPassword.required = true;
        labelPassword.innerText = 'Contraseña';
        helpPassword.innerText = '';
        form.reset();
    }

    function editarUsuario(id, nombre, email, rol, sucursal_id) {
        document.getElementById('modalTitle').innerText = 'Editar Usuario';
        document.getElementById('btnGuardar').innerText = 'Actualizar Usuario';
        form.action = '<?= BASE_URL ?>/usuario/guardar'; // El modelo actualiza si hay ID

        document.getElementById('usuarioId').value = id;
        document.getElementById('nombre').value = nombre;
        document.getElementById('email').value = email;
        document.getElementById('rol').value = rol;
        document.getElementById('sucursal_id').value = sucursal_id;

        // Contraseña opcional al editar
        inputPassword.required = false;
        inputPassword.value = '';
        labelPassword.innerText = 'Nueva Contraseña';
        helpPassword.innerText = 'Dejar vacío si no desea cambiar la contraseña.';
        
        modalUsuario.show();
    }
</script>

</body>
</html>
