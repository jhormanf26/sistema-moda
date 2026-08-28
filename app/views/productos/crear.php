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
                <form action="<?= BASE_URL ?>/producto/guardar" method="POST">
                    
                    <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Datos Básicos</h6>
                    
                    <div class="row mb-3 g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" required placeholder="Ej: Polo Algodón Estampado">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Código Base (Caja/Modelo)</label>
                            <input type="text" name="codigo" class="form-control" placeholder="Ej: MOD-001">
                        </div>
                        <div class="col-md-3">
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

                    <div class="row mb-4 g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted">Precio Costo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><?= htmlspecialchars($monedaEmpresa ?? 'S/') ?></span>
                                <input type="number" step="0.01" name="precio_compra" class="form-control border-start-0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-success">Precio Venta <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-success text-white border-success"><?= htmlspecialchars($monedaEmpresa ?? 'S/') ?></span>
                                <input type="number" step="0.01" name="precio_venta" class="form-control fw-bold border-success" required placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Descripción</label>
                            <input type="text" name="descripcion" class="form-control" placeholder="Detalles extra...">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-uppercase text-muted fw-bold m-0" style="font-size: 0.8rem; letter-spacing: 1px;">
                            Variantes (Inventario Inicial)
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-success fw-bold" onclick="agregarVariante()">
                            <i class="bi bi-plus-lg"></i> Agregar Talla/Color
                        </button>
                    </div>

                    <div class="table-responsive mb-4 rounded border">
                        <table class="table table-striped mb-0" id="tablaVariantes">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 20%;">Talla <span class="text-danger">*</span></th>
                                    <th style="width: 25%;">Color <span class="text-danger">*</span></th>
                                    <th style="width: 20%;">Stock Inicial <span class="text-danger">*</span></th>
                                    <th style="width: 25%;">Cód. Barras (Opcional)</th>
                                    <th style="width: 10%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" name="talla[]" class="form-control form-control-sm" required placeholder="Ej: M"></td>
                                    <td><input type="text" name="color[]" class="form-control form-control-sm" required placeholder="Ej: Negro"></td>
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
    // Función para agregar filas dinámicas
    function agregarVariante() {
        const tableBody = document.querySelector('#tablaVariantes tbody');
        
        const newRow = `
            <tr>
                <td><input type="text" name="talla[]" class="form-control form-control-sm" required placeholder="Talla"></td>
                <td><input type="text" name="color[]" class="form-control form-control-sm" required placeholder="Color"></td>
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
