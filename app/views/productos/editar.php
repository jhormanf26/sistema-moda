<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto - Sistema Moda</title>
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
        
        <div class="d-flex align-items-center mb-4">
            <a href="<?= BASE_URL ?>/producto/index" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="fw-bold m-0">Editar Producto</h2>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning bg-opacity-25 py-3 border-bottom">
                <h5 class="mb-0 text-dark"><i class="bi bi-pencil-square me-2"></i> Editando: <?= $p['nombre'] ?></h5>
            </div>
            
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/producto/actualizar" method="POST">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">

                    <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Datos Básicos</h6>

                    <div class="row mb-3 g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" required value="<?= $p['nombre'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Código Base</label>
                            <input type="text" name="codigo" class="form-control" value="<?= $p['codigo_barras_base'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Categoría</label>
                            <select name="categoria" class="form-select" required>
                                <?php if(isset($categorias)): ?>
                                    <?php foreach($categorias as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $p['categoria_id'] == $cat['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3 g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted">Precio Costo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><?= htmlspecialchars($monedaEmpresa ?? 'S/') ?></span>
                                <input type="number" step="0.01" name="precio_compra" class="form-control border-start-0" value="<?= $p['precio_compra'] ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-success">Precio Venta <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-success text-white border-success"><?= htmlspecialchars($monedaEmpresa ?? 'S/') ?></span>
                                <input type="number" step="0.01" name="precio_venta" class="form-control fw-bold border-success" required value="<?= $p['precio_venta'] ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Descripción</label>
                            <input type="text" name="descripcion" class="form-control" value="<?= $p['descripcion'] ?>">
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex align-items-center mb-3">
                        <h6 class="text-uppercase text-muted fw-bold m-0 me-2" style="font-size: 0.8rem; letter-spacing: 1px;">Inventario de Variantes</h6>
                        <span class="badge bg-info text-dark">Edición Rápida</span>
                    </div>

                    <div class="table-responsive rounded border mb-4">
                        <table class="table table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 35%;">Variante (Talla / Color)</th>
                                    <th style="width: 25%;">Stock Actual</th>
                                    <th style="width: 40%;">Código de Barras</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($variantes)): ?>
                                    <tr><td colspan="3" class="text-center text-muted py-3">Este producto no tiene variantes registradas.</td></tr>
                                <?php else: ?>
                                    <?php foreach($variantes as $v): ?>
                                        <tr>
                                            <td>
                                                <input type="hidden" name="var_id[]" value="<?= $v['id'] ?>">
                                                
                                                <span class="fw-bold badge bg-light text-dark border me-1"><?= $v['talla'] ?></span>
                                                <span class="text-muted"><?= $v['color'] ?></span>
                                            </td>
                                            <td>
                                                <input type="number" name="var_stock[]" class="form-control form-control-sm fw-bold text-center" 
                                                       value="<?= $v['stock_actual'] ?>" min="0" required>
                                            </td>
                                            <td>
                                                <input type="text" name="var_codigo[]" class="form-control form-control-sm font-monospace" 
                                                       value="<?= $v['codigo_barras_variante'] ?>" placeholder="Generar auto...">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="p-2 bg-white border-top text-muted small">
                            <i class="bi bi-info-circle me-1"></i> Puedes modificar el stock y códigos directamente aquí. Para agregar nuevas tallas, por favor crea un nuevo producto o usa el módulo de Compras.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>/producto/index" class="btn btn-light border px-4">Cancelar</a>
                        <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
