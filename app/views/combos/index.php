<?php
// app/views/combos/index.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combos & Packs Promocionales - Sistema Moda</title>
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

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="page-title"><i class="bi bi-gift-fill text-danger me-2"></i> Combos & Packs Promocionales</h2>
                <p class="text-muted mb-0">Crea ofertas combinando múltiples productos con precio especial para la tienda.</p>
            </div>
            <button class="btn btn-danger shadow-sm rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalNuevoCombo">
                <i class="bi bi-plus-lg me-1"></i> Crear Nuevo Combo
            </button>
        </div>

        <div class="glass-card table-responsive p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Combo</th>
                        <th>Productos Incluidos</th>
                        <th class="text-center">Precio Combo</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($combos)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-2 text-secondary"></i>
                                No se han creado combos o packs promocionales aún.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($combos as $combo): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-dark">
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($combo['imagen'])): ?>
                                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($combo['imagen']) ?>" alt="Combo" class="rounded me-3 object-fit-cover shadow-sm" style="width: 50px; height: 50px;">
                                    <?php else: ?>
                                        <div class="bg-danger bg-opacity-10 text-danger rounded me-3 d-flex align-items-center justify-content-center fw-bold" style="width: 50px; height: 50px;">
                                            <i class="bi bi-gift fs-4"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($combo['nombre']) ?></div>
                                        <div class="text-muted small font-monospace"><?= htmlspecialchars($combo['descripcion'] ?? 'Sin descripción') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <?php foreach ($combo['items'] as $it): ?>
                                        <span class="badge bg-light text-dark border text-start fw-normal">
                                            <i class="bi bi-box-arrow-in-right text-danger me-1"></i>
                                            <strong><?= $it['cantidad'] ?>x</strong> <?= htmlspecialchars($it['producto_nombre']) ?>
                                            <?php if ($it['producto_activo'] == 0): ?>
                                                <span class="text-danger ms-1 fw-bold">(Inactivo)</span>
                                            <?php endif; ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="fs-5 fw-bold text-success"><?= $empresa['moneda'] ?? '$' ?> <?= number_format($combo['precio'], 2) ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($combo['activo']): ?>
                                    <span class="badge bg-success rounded-pill px-3">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill px-3">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <!-- Editar Combo -->
                                <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEditarCombo<?= $combo['id'] ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <!-- Activar / Desactivar -->
                                <button onclick="toggleEstadoCombo(<?= $combo['id'] ?>, <?= $combo['activo'] ? 0 : 1 ?>)" class="btn btn-sm <?= $combo['activo'] ? 'btn-outline-warning' : 'btn-outline-success' ?> me-1" title="<?= $combo['activo'] ? 'Desactivar' : 'Activar' ?>">
                                    <i class="bi <?= $combo['activo'] ? 'bi-eye-slash' : 'bi-eye' ?>"></i>
                                </button>
                                <!-- Eliminar -->
                                <button onclick="eliminarCombo(<?= $combo['id'] ?>)" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
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

<!-- Modal Nuevo Combo -->
<div class="modal fade" id="modalNuevoCombo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="formNuevoCombo" onsubmit="guardarCombo(event, this)" class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-gift-fill me-2"></i> Crear Nuevo Combo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label text-muted fw-bold small">Nombre del Combo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required placeholder="Ej. Pack Beauty Skincare Essentials">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-bold small">Precio Promocional (<?= $empresa['moneda'] ?? '$' ?>) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="precio" class="form-control fw-bold text-success" required placeholder="0.00">
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted fw-bold small">Descripción (Opcional)</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Describe los beneficios o productos que componen este paquete promocional..."></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted fw-bold small">Foto Promocional del Combo (Opcional)</label>
                        <input type="file" name="imagen" class="form-control" accept="image/*">
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-boxes text-danger me-2"></i> Productos Incluidos en el Combo</h6>
                    <button type="button" onclick="agregarItemFila('itemsContainerNuevo')" class="btn btn-sm btn-outline-danger font-monospace fw-bold">
                        <i class="bi bi-plus-circle me-1"></i> Agregar Producto
                    </button>
                </div>

                <div id="itemsContainerNuevo" class="vstack gap-2">
                    <div class="row g-2 align-items-center item-row">
                        <div class="col-8">
                            <select name="items[0][producto_id]" class="form-select" required>
                                <option value="">Seleccione un producto...</option>
                                <?php foreach ($productos as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?> (<?= $empresa['moneda'] ?? '$' ?> <?= number_format($p['precio_venta'], 2) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted small">Cant.</span>
                                <input type="number" name="items[0][cantidad]" min="1" value="1" class="form-control text-center font-monospace fw-bold" required>
                            </div>
                        </div>
                        <div class="col-1 text-end">
                            <button type="button" onclick="eliminarFilaItem(this)" class="btn btn-outline-secondary btn-sm"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 d-flex justify-content-between">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm">Guardar Combo</button>
            </div>
        </form>
    </div>
</div>

<!-- Modales de Edición -->
<?php if (!empty($combos)): ?>
    <?php foreach($combos as $combo): ?>
        <div class="modal fade" id="modalEditarCombo<?= $combo['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <form onsubmit="guardarCombo(event, this)" class="modal-content border-0 shadow">
                    <input type="hidden" name="id" value="<?= $combo['id'] ?>">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Editar Combo #<?= $combo['id'] ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label text-muted fw-bold small">Nombre del Combo <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($combo['nombre']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold small">Precio Promocional (<?= $empresa['moneda'] ?? '$' ?>) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" name="precio" class="form-control fw-bold text-success" required value="<?= $combo['precio'] ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted fw-bold small">Descripción (Opcional)</label>
                                <textarea name="descripcion" class="form-control" rows="2"><?= htmlspecialchars($combo['descripcion'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted fw-bold small">Cambiar Foto (Opcional)</label>
                                <input type="file" name="imagen" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-boxes text-primary me-2"></i> Productos Incluidos</h6>
                            <button type="button" onclick="agregarItemFila('itemsContainerEdit<?= $combo['id'] ?>')" class="btn btn-sm btn-outline-primary font-monospace fw-bold">
                                <i class="bi bi-plus-circle me-1"></i> Agregar Producto
                            </button>
                        </div>

                        <div id="itemsContainerEdit<?= $combo['id'] ?>" class="vstack gap-2">
                            <?php foreach ($combo['items'] as $index => $it): ?>
                                <div class="row g-2 align-items-center item-row">
                                    <div class="col-8">
                                        <select name="items[<?= $index ?>][producto_id]" class="form-select" required>
                                            <option value="">Seleccione un producto...</option>
                                            <?php foreach ($productos as $p): ?>
                                                <option value="<?= $p['id'] ?>" <?= ($p['id'] == $it['producto_id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($p['nombre']) ?> (<?= $empresa['moneda'] ?? '$' ?> <?= number_format($p['precio_venta'], 2) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted small">Cant.</span>
                                            <input type="number" name="items[<?= $index ?>][cantidad]" min="1" value="<?= $it['cantidad'] ?>" class="form-control text-center font-monospace fw-bold" required>
                                        </div>
                                    </div>
                                    <div class="col-1 text-end">
                                        <button type="button" onclick="eliminarFilaItem(this)" class="btn btn-outline-secondary btn-sm"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 d-flex justify-content-between">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const BASE_URL_APP = '<?= BASE_URL ?>'.replace(/^http:/, window.location.protocol);

    const listaProductosHTML = `<?php foreach ($productos as $p): ?>
        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?> (<?= $empresa['moneda'] ?? '$' ?> <?= number_format($p['precio_venta'], 2) ?>)</option>
    <?php endforeach; ?>`;

    function agregarItemFila(containerId) {
        const container = document.getElementById(containerId);
        const nextIndex = container.children.length;
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-center item-row';
        row.innerHTML = `
            <div class="col-8">
                <select name="items[${nextIndex}][producto_id]" class="form-select" required>
                    <option value="">Seleccione un producto...</option>
                    ${listaProductosHTML}
                </select>
            </div>
            <div class="col-3">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted small">Cant.</span>
                    <input type="number" name="items[${nextIndex}][cantidad]" min="1" value="1" class="form-control text-center font-monospace fw-bold" required>
                </div>
            </div>
            <div class="col-1 text-end">
                <button type="button" onclick="eliminarFilaItem(this)" class="btn btn-outline-secondary btn-sm"><i class="bi bi-trash"></i></button>
            </div>
        `;
        container.appendChild(row);
    }

    function eliminarFilaItem(btn) {
        const row = btn.closest('.item-row');
        const container = row.parentElement;
        if (container.children.length > 1) {
            row.remove();
        } else {
            alert('El combo debe tener al menos un producto.');
        }
    }

    function guardarCombo(e, form) {
        e.preventDefault();
        const formData = new FormData(form);

        fetch(`${BASE_URL_APP}/combo/guardar`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(err => alert('Error de conexión al guardar combo.'));
    }

    function toggleEstadoCombo(id, nuevoEstado) {
        if (!confirm('¿Desea cambiar el estado del combo?')) return;
        const formData = new FormData();
        formData.append('id', id);
        formData.append('estado', nuevoEstado);

        fetch(`${BASE_URL_APP}/combo/cambiarEstado`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }

    function eliminarCombo(id) {
        if (!confirm('¿Está seguro de eliminar este combo?')) return;
        const formData = new FormData();
        formData.append('id', id);

        fetch(`${BASE_URL_APP}/combo/eliminar`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }
</script>
</body>
</html>
