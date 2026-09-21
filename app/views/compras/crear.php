<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Compra - Sistema Moda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Responsive Fix */
        @media (min-width: 992px) {
            .offcanvas-lg { position: fixed !important; top: 0; }
            .main-content-area { margin-left: 260px; width: calc(100% - 260px); }
        }
        @media (max-width: 767px) { .offcanvas-open { overflow: hidden !important; } }
        
        /* Buscador Flotante */
        .search-wrapper { position: relative; z-index: 1050; overflow: visible !important; }
        #resultados { 
            max-height: 250px; 
            overflow-y: auto; 
            position: absolute; 
            top: 100%; 
            left: 0; 
            width: 100%; 
            z-index: 1060; 
            display: none;
        }
    </style>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark d-lg-none shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">Sistema Moda</span>
        <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu"><i class="bi bi-list"></i> Menú</button>
    </div>
</nav>

<?php include '../app/views/layouts/sidebar.php'; ?>

<div class="main-content-area" style="height: 100vh; overflow-y: auto;">
    <div class="container-fluid p-4">
        
        <h2 class="fw-bold mb-4"><i class="bi bi-cart-plus-fill"></i> Ingreso de Mercadería (Compras)</h2>

        <div class="row">
            <div class="col-lg-8 col-md-12 mb-4">
                
                <div class="card shadow-sm border-0 mb-3" style="z-index: 1;">
                    <div class="card-header bg-white fw-bold">Datos de la Factura</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small text-muted fw-bold">PROVEEDOR</label>
                                <select id="proveedor_id" class="form-select">
                                    <?php if(isset($proveedores)): foreach($proveedores as $prov): ?>
                                        <option value="<?= $prov['id'] ?>"><?= $prov['razon_social'] ?> (<?= $prov['ruc'] ?>)</option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small text-muted fw-bold">NRO. COMPROBANTE</label>
                                <input type="text" id="comprobante" class="form-control" placeholder="Ej: F001-4568">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-3 search-wrapper">
                    <div class="card-body">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" id="buscador" class="form-control border-start-0" placeholder="Buscar producto..." autocomplete="off">
                        </div>
                        <div id="resultados" class="list-group shadow mt-1"></div>
                    </div>
                </div>

                <div class="card shadow-sm border-0" style="z-index: 0;">
                    <div class="card-header bg-dark text-white">Detalle de Productos</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr><th>Producto</th><th width="15%">Cantidad</th><th width="20%">Costo Unit.</th><th width="15%" class="text-end">Subtotal</th><th width="5%"></th></tr>
                                </thead>
                                <tbody id="carritoBody"></tbody>
                            </table>
                        </div>
                        <div id="emptyCart" class="text-center p-5 text-muted"><i class="bi bi-basket display-4 d-block mb-2"></i>Agrega productos</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                    <div class="card-body text-center p-4">
                        <h5 class="text-muted mb-3">Total a Pagar</h5>
                        <h1 class="fw-bold text-primary mb-4"><?= htmlspecialchars($monedaEmpresa ?? 'S/') ?> <span id="totalCompra">0.00</span></h1>
                        <div class="d-grid gap-2">
                            <button class="btn btn-success btn-lg fw-bold" onclick="guardarCompra()"><i class="bi bi-save me-2"></i> GUARDAR INGRESO</button>
                            <a href="<?= BASE_URL ?>/ventas" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </div>
                    <div class="card-footer bg-light text-muted small text-center">* Esto aumentará el stock.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const BASE_URL = '<?= BASE_URL ?>';
    const buscador = document.getElementById('buscador');
    const resultados = document.getElementById('resultados');
    const carritoBody = document.getElementById('carritoBody');
    const totalSpan = document.getElementById('totalCompra');
    const emptyCart = document.getElementById('emptyCart');
    
    let carrito = [];
    let resultadosBusqueda = []; 
    let timeoutBusqueda = null;

    buscador.addEventListener('keyup', function() {
        clearTimeout(timeoutBusqueda);
        const termino = this.value.trim();
        if(termino.length > 1) {
            timeoutBusqueda = setTimeout(() => {
                fetch(`${BASE_URL}/compra/buscar/${termino}`)
                    .then(r => r.json())
                    .then(data => { resultadosBusqueda = data; renderizarResultados(); })
                    .catch(err => { console.error(err); resultados.style.display = 'none'; });
            }, 300);
        } else { resultados.style.display = 'none'; }
    });

    function renderizarResultados() {
        resultados.innerHTML = '';
        if (resultadosBusqueda.length === 0) {
             resultados.innerHTML = '<div class="list-group-item text-muted">No encontrado.</div>';
        } else {
            resultadosBusqueda.forEach((p, index) => {
                let esPredeterminado = (
                    (!p.talla || p.talla === 'Única' || p.talla === 'Unica' || p.talla === 'Estándar' || p.talla === 'Estandar' || p.talla === 'N/A') &&
                    (!p.color || p.color === 'Estándar' || p.color === 'Estandar' || p.color === 'Único' || p.color === 'Unico' || p.color === 'N/A')
                );
                let varText = esPredeterminado ? 'General' : `${p.talla} / ${p.color}`;
                resultados.innerHTML += `<button class="list-group-item list-group-item-action" onclick="agregar(${index})"><div class="d-flex w-100 justify-content-between"><h6 class="mb-1 fw-bold text-primary">${p.nombre}</h6><small>Stock: ${p.stock_actual}</small></div><small class="text-muted">${varText}</small></button>`;
            });
        }
        resultados.style.display = 'block';
    }

    function agregar(index) {
        const prod = resultadosBusqueda[index];
        resultados.style.display = 'none'; buscador.value = '';
        
        let existe = carrito.find(i => i.variante_id === prod.variante_id);
        if(existe) { Swal.fire({ title: 'Ya agregado', icon: 'info', timer: 1000, showConfirmButton: false }); return; }
        
        // Si viene nulo, ponemos 0
        let costo = parseFloat(prod.precio_compra);
        if(isNaN(costo)) costo = 0;

        carrito.push({ variante_id: prod.variante_id, nombre: prod.nombre, talla: prod.talla, color: prod.color, cantidad: 1, costo: costo });
        renderizar();
    }

    function renderizar() {
        carritoBody.innerHTML = '';
        let total = 0;
        if(carrito.length > 0) emptyCart.style.display = 'none'; else emptyCart.style.display = 'block';

        const moneda = typeof G_MONEDA !== 'undefined' ? G_MONEDA : 'S/';

        carrito.forEach((item, index) => {
            let subtotal = item.cantidad * item.costo;
            total += subtotal;
            let esPredeterminado = (
                (!item.talla || item.talla === 'Única' || item.talla === 'Unica' || item.talla === 'Estándar' || item.talla === 'Estandar' || item.talla === 'N/A') &&
                (!item.color || item.color === 'Estándar' || item.color === 'Estandar' || item.color === 'Único' || item.color === 'Unico' || item.color === 'N/A')
            );
            let varText = esPredeterminado ? 'General' : `${item.talla} / ${item.color}`;

            carritoBody.innerHTML += `<tr><td><div class="fw-bold text-truncate" style="max-width: 200px;">${item.nombre}</div><small class="text-muted">${varText}</small></td><td><input type="number" class="form-control form-control-sm text-center" value="${item.cantidad}" min="1" onchange="upd(${index}, 'cantidad', this.value)"></td><td><div class="input-group input-group-sm"><span class="input-group-text">${moneda}</span><input type="number" class="form-control fw-bold" value="${item.costo}" min="0" step="1" onchange="upd(${index}, 'costo', this.value)"></div></td><td class="text-end fw-bold">${moneda} ${formatCOP(subtotal)}</td><td class="text-end"><button class="btn btn-sm btn-outline-danger border-0" onclick="del(${index})"><i class="bi bi-trash"></i></button></td></tr>`;
        });
        totalSpan.innerText = formatCOP(total);
    }

    window.upd = (idx, camp, val) => { 
        val = parseFloat(val); if(isNaN(val)) val = 0;
        if(camp === 'cantidad' && val < 1) val = 1; 
        carrito[idx][camp] = val; renderizar(); 
    }
    window.del = (idx) => { carrito.splice(idx, 1); renderizar(); }

    document.addEventListener('click', function(e) {
        if (!buscador.contains(e.target) && !resultados.contains(e.target)) resultados.style.display = 'none';
    });

    function guardarCompra() {
        if(carrito.length === 0) { Swal.fire('Error', 'Carrito vacío.', 'warning'); return; }
        
        const data = {
            proveedor_id: document.getElementById('proveedor_id').value,
            comprobante: document.getElementById('comprobante').value,
            carrito: carrito,
            total: document.getElementById('totalCompra').innerText
        };

        Swal.fire({ title: '¿Guardar?', icon: 'question', showCancelButton: true, confirmButtonText: 'Sí' }).then((r) => {
            if (r.isConfirmed) {
                fetch(`${BASE_URL}/compra/guardar`, {
                    method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data)
                })
                .then(r => r.json())
                .then(data => {
                    if(data.status) {
                        Swal.fire('¡Éxito!', 'Compra guardada.', 'success').then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(err => { console.error(err); Swal.fire('Error', 'Fallo al guardar.', 'error'); });
            }
        });
    }
</script>
</body>
</html>
