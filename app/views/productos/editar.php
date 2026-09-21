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
                <form action="<?= BASE_URL ?>/producto/actualizar" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">

                    <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Datos Básicos</h6>

                    <div class="row mb-3 g-3">
                        <div class="col-md-5">
                            <label class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($p['nombre']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Código Base</label>
                            <input type="text" name="codigo" class="form-control" value="<?= htmlspecialchars($p['codigo_barras_base']) ?>">
                        </div>
                        <div class="col-md-4">
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

                    <!-- SECCIÓN GALERÍA DE IMÁGENES -->
                    <div class="row mb-4 g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark"><i class="bi bi-images me-1"></i> Galería de Imágenes del Producto</label>
                            
                            <?php if(!empty($imagenes)): ?>
                                <div class="d-flex flex-wrap gap-3 mb-3 p-3 bg-white rounded border align-items-center">
                                    <?php foreach($imagenes as $img): ?>
                                        <div class="position-relative text-center p-2 rounded border <?= $img['es_principal'] ? 'border-warning bg-warning bg-opacity-10' : 'bg-light' ?>" style="width: 130px;">
                                            <?php if($img['es_principal']): ?>
                                                <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-1 shadow-sm">
                                                    <i class="bi bi-star-fill me-1"></i>Principal
                                                </span>
                                            <?php else: ?>
                                                <a href="<?= BASE_URL ?>/producto/marcarPrincipal/<?= $img['id'] ?>/<?= $p['id'] ?>" class="badge bg-secondary text-decoration-none position-absolute top-0 start-0 m-1 shadow-sm" title="Marcar como principal">
                                                    <i class="bi bi-star"></i> Hacer Principal
                                                </a>
                                            <?php endif; ?>

                                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($img['ruta_imagen']) ?>" alt="Imagen del producto" style="width: 110px; height: 110px; object-fit: cover; border-radius: 6px;" class="mb-2 shadow-sm">
                                            
                                            <div>
                                                <a href="<?= BASE_URL ?>/producto/eliminarImagen/<?= $img['id'] ?>/<?= $p['id'] ?>" 
                                                   class="btn btn-outline-danger btn-sm w-100 py-0" style="font-size: 0.75rem;"
                                                   onclick="return confirm('¿Deseas eliminar esta imagen?')">
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-light border text-muted small mb-3">
                                    <i class="bi bi-info-circle me-1"></i> No hay imágenes registradas para este producto.
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Subir Nuevas Imágenes <span class="text-muted fw-normal small">(Opcional)</span></label>
                            <input type="file" name="imagenes[]" class="form-control" accept="image/jpeg, image/png, image/jpg, image/webp" multiple onchange="previewImagenes(this)">
                            <div class="form-text">Puedes agregar más imágenes (JPG, PNG, WEBP).</div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div id="boxPreview" class="d-none mt-2 w-100">
                                <span class="text-muted small d-block mb-1">Vista previa de nuevas imágenes:</span>
                                <div id="galleryPreview" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3 g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted">Precio Costo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><?= htmlspecialchars($monedaEmpresa ?? '$') ?></span>
                                <input type="number" step="1" id="precio_compra" name="precio_compra" class="form-control border-start-0" value="<?= round($p['precio_compra']) ?>" oninput="actualizarFormatoMoneda(this, 'preview_costo')">
                            </div>
                            <small class="text-muted d-block mt-1" id="preview_costo"><?= htmlspecialchars($monedaEmpresa ?? '$') ?> <?= formatearCOP($p['precio_compra']) ?></small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-success">Precio Venta <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-success text-white border-success"><?= htmlspecialchars($monedaEmpresa ?? '$') ?></span>
                                <input type="number" step="1" id="precio_venta" name="precio_venta" class="form-control fw-bold border-success" required value="<?= round($p['precio_venta']) ?>" oninput="actualizarFormatoMoneda(this, 'preview_venta')">
                            </div>
                            <small class="text-success fw-bold d-block mt-1" id="preview_venta"><?= htmlspecialchars($monedaEmpresa ?? '$') ?> <?= formatearCOP($p['precio_venta']) ?></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Descripción</label>
                            <input type="text" name="descripcion" class="form-control" value="<?= htmlspecialchars($p['descripcion']) ?>">
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex align-items-center mb-3">
                        <h6 class="text-uppercase text-muted fw-bold m-0 me-2" style="font-size: 0.8rem; letter-spacing: 1px;">Inventario de Variantes / Presentaciones</h6>
                        <span class="badge bg-info text-dark">Edición Rápida</span>
                    </div>

                    <div class="table-responsive rounded border mb-4">
                        <table class="table table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 35%;">Variante (Talla / Presentación / Detalle)</th>
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
                                                
                                                <span class="fw-bold badge bg-light text-dark border me-1"><?= htmlspecialchars($v['talla']) ?></span>
                                                <span class="text-muted"><?= htmlspecialchars($v['color']) ?></span>
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
                            <i class="bi bi-info-circle me-1"></i> Puedes modificar el stock y códigos directamente aquí.
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
<script>
    function previewImagenes(input) {
        const box = document.getElementById('boxPreview');
        const container = document.getElementById('galleryPreview');
        container.innerHTML = '';

        if (input.files && input.files.length > 0) {
            box.classList.remove('d-none');
            Array.from(input.files).forEach((file) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.style.width = '70px';
                    div.style.height = '70px';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '8px';
                    img.style.border = '1px solid #ddd';
                    img.style.padding = '2px';
                    img.style.background = '#fff';

                    div.appendChild(img);
                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        } else {
            box.classList.add('d-none');
        }
    }

    function actualizarFormatoMoneda(input, elementId) {
        const val = parseFloat(input.value);
        const target = document.getElementById(elementId);
        if (target) {
            if (!isNaN(val) && val > 0) {
                target.innerText = G_MONEDA + ' ' + formatCOP(val);
            } else {
                target.innerText = '';
            }
        }
    }
</script>
</body>
</html>
