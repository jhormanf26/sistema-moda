<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Códigos de Barra - Sistema Moda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* FIX DE LAYOUT CRÍTICO: Asegura que el contenido se mueva a la derecha del sidebar fijo */
        @media (min-width: 992px) {
            /* Sidebar fijo en escritorio */
            .offcanvas-lg { position: fixed !important; top: 0; }
            /* Contenido desplazado a la derecha */
            .main-content-area { 
                margin-left: 260px; /* Ancho del sidebar */
                width: calc(100% - 260px);
            }
        }
        /* Fix de scroll para móviles */
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
        
        <h2 class="fw-bold mb-4"><i class="bi bi-upc-scan"></i> Generador de Etiquetas / Códigos de Barra</h2>
        <p class="text-muted">Selecciona los productos y la cantidad de etiquetas que deseas imprimir.</p>

        <form action="<?= BASE_URL ?>/etiqueta/generar" method="POST" target="_blank">
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 text-primary fw-bold">Selección de Productos</h5>
                    <button type="submit" class="btn btn-warning text-dark fw-bold" id="btnGenerar" disabled>
                        <i class="bi bi-printer-fill"></i> Generar PDF
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">
                                        <input type="checkbox" class="form-check-input" onclick="toggleAll(this)" title="Seleccionar Todos">
                                    </th>
                                    <th>ID</th>
                                    <th>Nombre Producto</th>
                                    <th>Precio Venta</th>
                                    <th>Código Principal</th>
                                    <th width="15%">Cant. Etiquetas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Verificamos si hay productos
                                if(empty($productos)): ?>
                                    <tr><td colspan="6" class="text-center py-4"><h4>No hay productos activos para etiquetar.</h4></td></tr>
                                <?php else: ?>
                                    <?php foreach($productos as $p): ?>
                                    <?php if($p['activo'] == 1): ?>
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="productos[]" value="<?= $p['id'] ?>" class="form-check-input chk-prod">
                                        </td>
                                        <td><?= $p['id'] ?></td>
                                        <td class="fw-bold"><?= $p['nombre'] ?></td>
                                        <td class="fw-bold text-success"><?= htmlspecialchars($monedaEmpresa ?? 'S/') ?> <?= number_format($p['precio_venta'], 2) ?></td>
                                        <td>
                                            <span class="font-monospace text-muted bg-light border px-1 rounded">
                                                <?= !empty($p['codigo_barras_base']) ? $p['codigo_barras_base'] : str_pad($p['id'], 8, '0', STR_PAD_LEFT) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <input type="number" name="cantidad[<?= $p['id'] ?>]" 
                                                   class="form-control form-control-sm text-center input-cantidad" value="1" min="1" disabled>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Lógica para habilitar/deshabilitar inputs de cantidad según selección
    document.querySelectorAll('.chk-prod').forEach(checkbox => {
        const inputCantidad = checkbox.closest('tr').querySelector('.input-cantidad');

        checkbox.addEventListener('change', function() {
            inputCantidad.disabled = !this.checked;
            // Si se marca, aseguramos que tenga al menos 1
            if (this.checked) {
                if(inputCantidad.value < 1) inputCantidad.value = 1;
            }
            actualizarBotonGenerar();
        });
    });

    // Seleccionar todos los checkboxes
    function toggleAll(source) {
        document.querySelectorAll('.chk-prod').forEach(checkbox => {
            checkbox.checked = source.checked;
            checkbox.dispatchEvent(new Event('change')); // Disparar evento manualmente para activar lógica
        });
    }

    // Habilitar botón "Generar PDF" solo si hay seleccionados
    function actualizarBotonGenerar() {
        const checkedCount = document.querySelectorAll('.chk-prod:checked').length;
        document.getElementById('btnGenerar').disabled = checkedCount === 0;
    }
    
    // Inicializar estado al cargar
    document.addEventListener('DOMContentLoaded', actualizarBotonGenerar);
</script>

</body>
</html>
