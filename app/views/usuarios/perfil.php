<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Sistema Moda</title>
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
        
        <h2 class="fw-bold mb-4"><i class="bi bi-person-gear"></i> Mi Perfil de Usuario</h2>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill me-2"></i> Contraseña actualizada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <?php if($_GET['error'] == 'mismatch'): ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-2"></i> Las nuevas contraseñas no coinciden.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif($_GET['error'] == 'wrong_pass'): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-x-circle-fill me-2"></i> La contraseña actual es incorrecta.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold py-3">Información Personal y Credenciales</div>
                    <div class="card-body p-4">
                        <form action="<?= BASE_URL ?>/perfil/guardar" method="POST">
                            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control bg-light" id="nombre" name="nombre" value="<?= $usuario['nombre'] ?>" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Usuario (Login)</label>
                                <input type="text" class="form-control bg-light" id="email" name="email" value="<?= $usuario['email'] ?>" readonly>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h6 class="mb-3 fw-bold text-primary">Cambiar Contraseña</h6>

                            <div class="mb-3">
                                <label for="clave_actual" class="form-label fw-bold">Contraseña Actual</label>
                                <input type="password" name="clave_actual" class="form-control" required placeholder="Requerido para guardar cambios">
                            </div>
                            
                            <div class="mb-3">
                                <label for="clave_nueva" class="form-label">Nueva Contraseña</label>
                                <input type="password" name="clave_nueva" class="form-control" placeholder="Mínimo 6 caracteres" minlength="6">
                            </div>
                            <div class="mb-4">
                                <label for="clave_confirmar" class="form-label">Confirmar Nueva Contraseña</label>
                                <input type="password" name="clave_confirmar" class="form-control" placeholder="Repite la nueva clave" minlength="6">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-save me-2"></i> Actualizar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0 bg-white">
                    <div class="card-header bg-white fw-bold py-3">Detalles de la Cuenta</div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">ROL DE ACCESO</label>
                            <div class="mt-1">
                                <span class="badge bg-<?= $usuario['rol'] == 'admin' ? 'danger' : 'success' ?> text-uppercase p-2">
                                    <?= $usuario['rol'] ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">ESTADO</label>
                            <div class="mt-1">
                                <span class="badge bg-success p-2">Activo</span>
                            </div>
                        </div>

                        <div class="alert alert-light border mt-4">
                            <i class="bi bi-info-circle-fill me-2 text-info"></i>
                            <small>
                                Como <strong><?= ucfirst($usuario['rol']) ?></strong>, tienes acceso a las funciones asignadas por el sistema. Si necesitas cambiar tu nombre o correo, contacta al soporte técnico.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
