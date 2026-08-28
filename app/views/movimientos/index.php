<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mermas y Devoluciones - Sistema Moda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        /* Estilo para el buscador de resultados */
        #resultados { max-height: 250px; overflow-y: auto; position: absolute; z-index: 1000; width: 100%; border: 1px solid #dee2e6; border-top: none; background-color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .list-group-item:hover { background-color: #f8f9fa; cursor: pointer; }
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
        
        <h2 class="fw-bold mb-4"><i class="bi bi-arrow-down-up"></i> Ajuste Manual de Inventario (Kardex)</h2>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill"></i> Movimiento registrado correctamente.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php elseif(isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-octagon-fill"></i> Error al registrar: <?= htmlspecialchars($_GET['error']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold">Registrar Entrada (Devolución) o Salida (Merma)</div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/movimiento/guardar" method="POST" id="formAjuste">
                    <input type="hidden" name="id_variante" id="id_variante_submit" required>

                    <div class="row">
                        <div class="col-md-6 mb-4 position-relative">
                            <label class="form-label fw-bold">Buscar Producto y Variante</label>
                            <input type="text" id="buscador" class="form-control form-control-lg" placeholder="Escribe el nombre o escanea el código..." autocomplete="off">
                            <div id="resultados" class="list-group"></div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Producto Seleccionado</label>
                            <div class="alert alert-secondary py-3 text-center" id="productoSeleccionado">
                                <span class="text-muted">Busca y selecciona un producto para continuar</span>
                            </div>
                        </div>
                    </div>
                    
                    <hr>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="tipo" class="form-label fw-bold">Tipo de Movimiento</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="salida">🔴 SALIDA (Merma)</option>
                                <option value="entrada">🟢 ENTRADA (Devolución)</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="cantidad" class="form-label fw-bold">Cantidad</label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" value="1" required disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="motivo" class="form-label fw-bold">Motivo (Justificación)</label>
                            <textarea name="motivo" class="form-control" id="motivo" rows="2" required disabled placeholder="Ej: Prenda con daño, Devolución por ajuste de talla, Inventario físico..."></textarea>
                        </div>
                    </div>
                    
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary" id="btnGuardar" disabled>
                            <i class="bi bi-save"></i> Registrar Movimiento
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const BASE_URL = '<?= BASE_URL ?>';
    const buscador = document.getElementById('buscador');
    const resultados = document.getElementById('resultados');
    const productoSeleccionadoDiv = document.getElementById('productoSeleccionado');
    const idVarianteSubmit = document.getElementById('id_variante_submit');
    const cantidadInput = document.getElementById('cantidad');
    const motivoTextarea = document.getElementById('motivo');
    const btnGuardar = document.getElementById('btnGuardar');
    const formAjuste = document.getElementById('formAjuste');
    let maxStock = 0;

    // --- Lógica de Búsqueda AJAX ---
    buscador.addEventListener('keyup', function() {
        let termino = this.value.trim();
        if (termino.length > 2) {
            fetch(`${BASE_URL}/ventas/buscar/${termino}`)
                .then(res => res.json())
                .then(data => {
                    resultados.innerHTML = '';
                    if(data.length == 0) {
                        resultados.innerHTML = '<div class="list-group-item text-center text-muted">Producto no encontrado.</div>';
                        return;
                    }
                    data.forEach(p => {
                        resultados.innerHTML += `
                            <button type="button" class="list-group-item list-group-item-action" 
                                onclick="seleccionarVariante(${p.variante_id}, '${p.nombre}', '${p.talla}', '${p.color}', ${p.stock_actual})">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${p.nombre}</strong><br>
                                        <small class="text-muted">${p.talla} / ${p.color}</small>
                                    </div>
                                    <span class="badge bg-info">Stock: ${p.stock_actual}</span>
                                </div>
                            </button>
                        `;
                    });
                });
        } else {
            resultados.innerHTML = '';
        }
    });

    // --- Selección y Habilitación de Formulario ---
    window.seleccionarVariante = (id, nombre, talla, color, stock) => {
        resultados.innerHTML = '';
        idVarianteSubmit.value = id;
        maxStock = stock;
        
        productoSeleccionadoDiv.innerHTML = `
            <strong>${nombre}</strong><br>
            Variante: ${talla} - ${color}<br>
            Stock Actual: <strong class="text-primary">${stock}</strong>
        `;
        productoSeleccionadoDiv.className = 'alert alert-info py-3 text-center';

        cantidadInput.disabled = false;
        motivoTextarea.disabled = false;
        btnGuardar.disabled = false;
    }

    // --- Validar al enviar ---
    formAjuste.addEventListener('submit', function(e) {
        const tipo = document.getElementById('tipo').value;
        const cantidad = parseInt(cantidadInput.value);

        if (tipo === 'salida' && cantidad > maxStock) {
            e.preventDefault();
            Swal.fire('Error de Stock', `No puedes retirar ${cantidad} unidades. Solo hay ${maxStock} en el inventario.`, 'warning');
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        btnGuardar.disabled = true;
        motivoTextarea.disabled = true;
        cantidadInput.disabled = true;
    });
</script>

</body>
</html>
