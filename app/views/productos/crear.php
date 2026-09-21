<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Producto - Sistema Moda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* FIX DE LAYOUT CRÍTICO */
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
            <h2 class="fw-bold m-0">Registrar Nuevo Producto</h2>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 text-primary"><i class="bi bi-bag-plus-fill me-2"></i> Información del Artículo</h5>
            </div>
            
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/producto/guardar" method="POST" enctype="multipart/form-data">
                    
                    <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Datos Básicos</h6>
                    
                    <div class="row mb-3 g-3">
                        <div class="col-md-5">
                            <label class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" required placeholder="Ej: Crema Humectante / Polo Algodón">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Código Base (Caja/Modelo)</label>
                            <input type="text" name="codigo" class="form-control" placeholder="Ej: MOD-001">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoría</label>
                            <select name="categoria" class="form-select" required>
                                <option value="" disabled selected>Seleccione...</option>
                                <?php if(isset($categorias)): ?>
                                    <?php foreach($categorias as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3 g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Imágenes del Producto <span class="text-muted fw-normal small">(Opcional)</span></label>
                            <input type="file" name="imagenes[]" class="form-control" accept="image/jpeg, image/png, image/jpg, image/webp" multiple onchange="previewImagenes(this)">
                            <div class="form-text">Puedes seleccionar una o múltiples imágenes (JPG, PNG, WEBP). La primera será la principal.</div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div id="boxPreview" class="d-none mt-2 w-100">
                                <span class="text-muted small d-block mb-1">Vista previa de imágenes a subir:</span>
                                <div id="galleryPreview" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4 g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted">Precio Costo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><?= htmlspecialchars($monedaEmpresa ?? '$') ?></span>
                                <input type="number" step="1" id="precio_compra" name="precio_compra" class="form-control border-start-0" placeholder="Ej: 15000" oninput="actualizarFormatoMoneda(this, 'preview_costo')">
                            </div>
                            <small class="text-muted d-block mt-1" id="preview_costo"></small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-success">Precio Venta <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-success text-white border-success"><?= htmlspecialchars($monedaEmpresa ?? '$') ?></span>
                                <input type="number" step="1" id="precio_venta" name="precio_venta" class="form-control fw-bold border-success" required placeholder="Ej: 20000" oninput="actualizarFormatoMoneda(this, 'preview_venta')">
                            </div>
                            <small class="text-success fw-bold d-block mt-1" id="preview_venta"></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Descripción</label>
                            <input type="text" name="descripcion" class="form-control" placeholder="Detalles extra...">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-uppercase text-muted fw-bold m-0" style="font-size: 0.8rem; letter-spacing: 1px;">
                            Variantes / Presentación (Inventario Inicial)
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-success fw-bold" onclick="agregarVariante()">
                            <i class="bi bi-plus-lg"></i> Agregar Variante / Presentación
                        </button>
                    </div>

                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i> Para productos generales (cremas, cosméticos, aseo, etc.), puedes dejar Talla y Color en blanco o colocar detalles (ej. 200ml, Tono 01, etc.). Se asignará <strong>Única / Estándar</strong> automáticamente si no los especificas.
                    </p>

                    <div class="table-responsive mb-4 rounded border">
                        <table class="table table-striped mb-0" id="tablaVariantes">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 25%;">Talla / Presentación <span class="text-muted fw-normal small">(Opcional)</span></th>
                                    <th style="width: 25%;">Color / Detalle <span class="text-muted fw-normal small">(Opcional)</span></th>
                                    <th style="width: 20%;">Stock Inicial <span class="text-danger">*</span></th>
                                    <th style="width: 20%;">Cód. Barras (Opcional)</th>
                                    <th style="width: 10%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" name="talla[]" class="form-control form-control-sm" placeholder="Ej: 200ml, Única, M"></td>
                                    <td><input type="text" name="color[]" class="form-control form-control-sm" placeholder="Ej: Estándar, Rojo, N/A"></td>
                                    <td><input type="number" name="stock[]" class="form-control form-control-sm" required value="0" min="0"></td>
                                    <td><input type="text" name="codigo_var[]" class="form-control form-control-sm" placeholder="Auto"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-light text-muted btn-sm border" disabled title="La primera fila es obligatoria">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= BASE_URL ?>/producto/index" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i> GUARDAR PRODUCTO
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
            Array.from(input.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'position-relative';
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

                    if (index === 0) {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-warning text-dark position-absolute top-0 start-0 m-1 shadow-sm';
                        badge.style.fontSize = '0.65rem';
                        badge.innerText = '★';
                        badge.title = 'Principal';
                        div.appendChild(badge);
                    }

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

    // Función para agregar filas dinámicas
    function agregarVariante() {
        const tableBody = document.querySelector('#tablaVariantes tbody');
        
        const newRow = `
            <tr>
                <td><input type="text" name="talla[]" class="form-control form-control-sm" placeholder="Ej: 200ml, M"></td>
                <td><input type="text" name="color[]" class="form-control form-control-sm" placeholder="Ej: Estándar, Rojo"></td>
                <td><input type="number" name="stock[]" class="form-control form-control-sm" required value="0" min="0"></td>
                <td><input type="text" name="codigo_var[]" class="form-control form-control-sm" placeholder="Auto"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm border" onclick="this.closest('tr').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        // Insertar HTML al final del tbody
        tableBody.insertAdjacentHTML('beforeend', newRow);
    }
</script>

</body>
</html>
