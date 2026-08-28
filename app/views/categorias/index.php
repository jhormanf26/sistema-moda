<?php
// app/views/categorias/index.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - Sistema Moda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark d-lg-none shadow-sm flex-shrink-0">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">Sistema Moda</span>
        <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
            <i class="bi bi-list"></i>
        </button>
    </div>
</nav>

<?php include '../app/views/layouts/sidebar.php'; ?>

<div class="main-content-area" style="min-height: 100vh; overflow-y: auto;">
    <div class="container-fluid p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title"><i class="bi bi-tags text-primary me-2"></i> Gestión de Categorías</h2>
                <p class="text-muted mb-0">Agrupa tus productos para un mejor control y filtrado en TPV.</p>
            </div>
            <button class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalNuevaCategoria">
                <i class="bi bi-plus-lg me-1"></i> Nueva Categoría
            </button>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> Operación realizada con éxito.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="glass-card table-responsive p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nombre de Categoría</th>
                        <th>Descripción</th>
                        <th class="text-center">Cant. Productos</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categorias)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No hay categorías registradas.</td></tr>
                    <?php else: ?>
                        <?php foreach($categorias as $cat): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-dark">
                                <div class="d-flex align-items-center">
                                    <div class="kpi-icon-wrap bg-primary bg-opacity-10 text-primary me-3" style="width: 40px; height: 40px; border-radius: 8px;">
                                        <i class="bi bi-tag-fill fs-5"></i>
                                    </div>
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </div>
                            </td>
                            <td class="text-muted small"><?= htmlspecialchars($cat['descripcion'] ?? 'Sin descripción') ?></td>
                            <td class="text-center fw-bold">
                                <span class="badge bg-light text-dark border"><?= $cat['total_productos'] ?> unids.</span>
                            </td>
                            <td class="text-center">
                                <?php if($cat['activo']): ?>
                                    <span class="badge bg-success rounded-pill px-3">Activa</span>
                                <?php else: ?>
                                    <span class="badge bg-danger rounded-pill px-3">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <!-- Botón Editar -->
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditarCategoria<?= $cat['id'] ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                
                                <!-- Botón Desactivar / Activar -->
                                <?php if($cat['activo']): ?>
                                    <a href="<?= BASE_URL ?>/categoria/desactivar/<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Desactivar esta categoría? Ocultará los productos en el TPV.')">
                                        <i class="bi bi-eye-slash"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>/categoria/activar/<?= $cat['id'] ?>" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-eye"></i>
                                    </a>
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

<?php if (!empty($categorias)): ?>
    <?php foreach($categorias as $cat): ?>
        <!-- Modal Editar -->
        <div class="modal fade" id="modalEditarCategoria<?= $cat['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form action="<?= BASE_URL ?>/categoria/actualizar" method="POST" class="modal-content border-0 shadow">
                    <div class="modal-header bg-light border-bottom-0">
                        <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i> Editar Categoría</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label text-muted fw-bold small">Nombre de la Categoría <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($cat['nombre']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted fw-bold small">Descripción (Opcional)</label>
                            <textarea name="descripcion" class="form-control" rows="2"><?= htmlspecialchars($cat['descripcion'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 d-flex justify-content-between">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Modal Nueva Categoría -->
<div class="modal fade" id="modalNuevaCategoria" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= BASE_URL ?>/categoria/guardar" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-bookmark-plus text-primary me-2"></i> Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label text-muted fw-bold small">Nombre de la Categoría <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control" required placeholder="Ej. Accesorios">
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted fw-bold small">Descripción (Opcional)</label>
                    <textarea name="descripcion" class="form-control" rows="2" placeholder="Ej. Lentes, relojes, cinturones..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top-0 d-flex justify-content-between">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Crear Categoría</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
