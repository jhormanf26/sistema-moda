<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta - Sistema Moda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    
    <style>
        /* Specific TPV Styles that override or extend global ones */
        body {
            overflow: hidden;
            background-color: var(--bg-main);
        }

        .main-content-area {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .scroll-area {
            height: calc(100vh - 150px);
            overflow-y: auto;
            padding-right: 5px;
        }

        .card-producto {
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid var(--border-color);
            background: var(--bg-card);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
        }

        .card-producto:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
        }

        .table-carrito th {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .total-section {
            background-color: #fff;
            border-top: 2px solid var(--primary);
        }
        
        @media (max-width: 767px) {
            .scroll-area {
                height: calc(100vh - 220px) !important;
            }
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark d-lg-none shadow-sm flex-shrink-0">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold"><i class="bi bi-shop-window"></i> TPV</span>
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu"
                aria-controls="sidebarMenu">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </nav>

    <?php include '../app/views/layouts/sidebar.php'; ?>

    <div class="main-content-area">

        <div class="bg-white shadow-sm p-2 d-none d-lg-flex justify-content-between align-items-center px-4">
            <h5 class="m-0 fw-bold text-primary"><i class="bi bi-shop-window"></i> Punto de Venta</h5>
            <div>
                <a href="<?= BASE_URL ?>/reporte/index" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="bi bi-speedometer2"></i> Panel
                </a>
                <span class="badge bg-success p-2">
                    <i class="bi bi-person-circle"></i> <?= $_SESSION['user_nombre'] ?>
                </span>
            </div>
        </div>

        <div class="container-fluid h-100 p-0">
            <div class="row g-0 h-100">

                <div class="col-lg-8 col-md-7 p-3 d-flex flex-column h-100">
                    <div class="card shadow-sm mb-3 flex-shrink-0">
                        <div class="card-body p-2">
                            
                            <!-- Filtro Rápido por Categorías -->
                            <div class="mb-2 overflow-auto" style="white-space: nowrap; padding-bottom: 5px;">
                                <button class="btn btn-sm btn-dark rounded-pill px-3 me-2" onclick="filtrarCategoria('todas')">Todas</button>
                                <?php if(isset($categoriasActivas)): ?>
                                    <?php foreach($categoriasActivas as $cat): ?>
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2" onclick="filtrarCategoria(<?= $cat['id'] ?>)">
                                            <?= htmlspecialchars($cat['nombre']) ?>
                                        </button>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="buscador" class="form-control border-start-0"
                                    placeholder="Escanear código o buscar producto..." autofocus autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="row scroll-area" id="gridProductos">
                        <div class="col-12 text-center text-muted mt-5">
                            <h4>📦 Listo para vender</h4>
                            <p>Escanea un código o escribe el nombre del producto.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-5 bg-white border-start shadow-sm h-100 d-flex flex-column">

                    <div
                        class="p-3 bg-primary text-white d-flex justify-content-between align-items-center flex-shrink-0">
                        <h5 class="m-0"><i class="bi bi-cart4"></i> Ticket</h5>
                        <button class="btn btn-sm btn-outline-light" onclick="limpiarCarrito()">
                            <i class="bi bi-trash"></i> Limpiar
                        </button>
                    </div>

                    <div
                        class="bg-light p-2 border-bottom d-flex justify-content-between align-items-center flex-shrink-0">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Cliente:</small>
                            <span class="fw-bold text-dark text-truncate d-inline-block" style="max-width: 150px;"
                                id="nombreCliente">Público General</span>
                        </div>
                        <button class="btn btn-sm btn-outline-primary bg-white" onclick="buscarClienteModal()">
                            <i class="bi bi-person-lines-fill"></i> Cambiar
                        </button>
                    </div>

                    <div class="flex-grow-1 overflow-auto">
                        <table class="table table-hover table-carrito mb-0">
                            <thead>
                                <tr>
                                    <th>Desc.</th>
                                    <th width="15%" class="text-center">Cant.</th>
                                    <th width="20%" class="text-end">Total</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody id="carritoBody"></tbody>
                        </table>
                    </div>

                    <div class="p-3 total-section flex-shrink-0">
                        <div class="d-flex justify-content-between mb-3">
                            <h3 class="fw-light">Total:</h3>
                            <h3 class="fw-bold text-success"><?= $monedaEmpresa ?? 'S/' ?> <span
                                    id="totalVenta">0.00</span></h3>
                        </div>
                        <button class="btn btn-success w-100 btn-lg py-3 fw-bold shadow" onclick="cobrar()">
                            <i class="bi bi-credit-card-2-back"></i> COBRAR
                        </button>
                    </div>

                    <div class="d-lg-none" style="height: 60px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cobrar -->
    <div class="modal fade" id="modalCobro" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-cash-coin"></i> Procesar Pago</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <h3 class="text-center mb-4">Total a Pagar: <span id="cobroTotalTexto" class="text-success fw-bold"></span></h3>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Método de Pago</label>
                        <select class="form-select form-select-lg" id="formaPagoCobro" onchange="verificarFormaPago()">
                            <option value="efectivo">Efectivo 💵</option>
                            <option value="yape">Yape 📱</option>
                            <option value="plin">Plin 📱</option>
                            <option value="tarjeta">Tarjeta 💳</option>
                            <option value="transferencia">Transferencia 🏦</option>
                        </select>
                    </div>

                    <div id="divMontoRecibido">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Monto Recibido</label>
                            <input type="number" class="form-control form-control-lg text-center fw-bold text-success" id="montoRecibido" onkeyup="calcularVuelto()" step="0.10" placeholder="0.00">
                        </div>
                        <h4 class="text-center mt-3">Vuelto: <span id="vueltoTexto" class="text-primary fw-bold">0.00</span></h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success fw-bold px-4" onclick="confirmarVentaExterna()">Confirmar Venta</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const BASE_URL = '<?= BASE_URL ?>';
        const buscador = document.getElementById('buscador');
        const grid = document.getElementById('gridProductos');
        const carritoBody = document.getElementById('carritoBody');
        const totalSpan = document.getElementById('totalVenta');

        let carrito = [];
        let clienteActual = { id: 1, nombre: 'Público General' };
        let timeoutBusqueda = null;

        // --- CLIENTES ---
        window.buscarClienteModal = async () => {
            const { value: termino } = await Swal.fire({
                title: 'Buscar Cliente',
                input: 'text',
                inputPlaceholder: 'Nombre o DNI/RUC...',
                showCancelButton: true,
                confirmButtonText: 'Buscar'
            });

            if (termino) {
                fetch(`${BASE_URL}/cliente/buscar/${termino}`)
                    .then(res => res.json())
                    .then(clientes => {
                        if (clientes.length === 0) {
                            Swal.fire('No encontrado', 'No existe. Crea el cliente en el panel.', 'info');
                        } else {
                            seleccionarDeLista(clientes);
                        }
                    });
            }
        };

        function seleccionarDeLista(clientes) {
            let html = '<div class="list-group text-start">';
            clientes.forEach(c => {
                html += `
                <button type="button" class="list-group-item list-group-item-action" 
                    onclick="setCliente(${c.id}, '${c.nombre}')">
                    <strong>${c.nombre}</strong> <br> 
                    <small class="text-muted">${c.documento || 'Sin Doc'}</small>
                </button>
            `;
            });
            html += '</div>';

            Swal.fire({
                title: 'Selecciona un Cliente',
                html: html,
                showConfirmButton: false,
                showCancelButton: true
            });
        }

        window.setCliente = (id, nombre) => {
            clienteActual = { id: id, nombre: nombre };
            document.getElementById('nombreCliente').innerText = nombre;
            Swal.close();
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1000 });
            Toast.fire({ icon: 'success', title: 'Cliente Asignado' });
        }

        // --- PRODUCTOS ---
        buscador.addEventListener('keyup', function (e) {
            clearTimeout(timeoutBusqueda);
            const termino = this.value.trim();
            if (e.key === 'Enter' && termino.length > 0) { buscarProductos(termino); return; }
            if (termino.length > 2) { timeoutBusqueda = setTimeout(() => { buscarProductos(termino); }, 300); }
            if (termino.length === 0) { grid.innerHTML = '<div class="col-12 text-center text-muted mt-5"><h4>📦 Listo para vender</h4><p>Escanea un código o escribe el nombre del producto.</p></div>'; }
        });

        function buscarProductos(termino) {
            fetch(`${BASE_URL}/ventas/buscar/${termino}`)
                .then(response => response.json())
                .then(data => renderizarProductos(data));
        }

        window.filtrarCategoria = (id_categoria) => {
            fetch(`${BASE_URL}/ventas/buscarPorCategoria/${id_categoria}`)
                .then(response => response.json())
                .then(data => renderizarProductos(data));
        };

        function renderizarProductos(productos) {
            grid.innerHTML = '';
            if (productos.length === 0) {
                grid.innerHTML = '<div class="col-12 text-center text-danger mt-5"><h5>🚫 Producto no encontrado</h5></div>';
                return;
            }
            const moneda = typeof G_MONEDA !== 'undefined' ? G_MONEDA : 'S/';
            productos.forEach(prod => {
                let stockClass = prod.stock_actual < 5 ? 'bg-danger' : 'bg-success';
                let stockText = prod.stock_actual < 1 ? 'AGOTADO' : `Stock: ${prod.stock_actual}`;
                let disabled = prod.stock_actual < 1 ? 'opacity: 0.6; pointer-events: none;' : '';

                let esPredeterminado = (
                    (!prod.talla || prod.talla === 'Única' || prod.talla === 'Unica' || prod.talla === 'Estándar' || prod.talla === 'Estandar' || prod.talla === 'N/A') &&
                    (!prod.color || prod.color === 'Estándar' || prod.color === 'Estandar' || prod.color === 'Único' || prod.color === 'Unico' || prod.color === 'N/A')
                );

                let badgeHtml = '';
                if (!esPredeterminado) {
                    badgeHtml = `
                    <div class="mb-2">
                        <span class="badge bg-light text-dark border">${prod.talla}</span>
                        <span class="badge bg-light text-dark border">${prod.color}</span>
                    </div>`;
                }

                let mediaHtml = (prod.imagen && prod.imagen.trim() !== '') 
                    ? `<img src="${BASE_URL}/${prod.imagen}" alt="${prod.nombre}" class="img-fluid rounded mb-2" style="height: 85px; width: 100%; object-fit: contain;">`
                    : `<div class="mb-2 fs-1 text-primary py-2"><i class="bi bi-tag-fill"></i></div>`;

                let card = `
                <div class="col-lg-3 col-md-4 col-6 mb-3" style="${disabled}">
                    <div class="card card-producto h-100" onclick='agregarAlCarrito(${JSON.stringify(prod)})'>
                        <div class="card-body p-2 text-center position-relative d-flex flex-column justify-content-between">
                            <span class="position-absolute top-0 end-0 badge ${stockClass} m-1" style="font-size:0.65rem; z-index:2;">${stockText}</span>
                            <div>
                                ${mediaHtml}
                                <h6 class="card-title text-truncate fw-bold mb-1" title="${prod.nombre}">${prod.nombre}</h6>
                                ${badgeHtml}
                            </div>
                            <h5 class="text-primary fw-bold mb-0 mt-1">${moneda} ${formatCOP(prod.precio_venta)}</h5>
                        </div>
                    </div>
                </div>`;
                grid.innerHTML += card;
            });
        }

        // --- CARRITO ---
        window.agregarAlCarrito = function (producto) {
            const existe = carrito.find(item => item.variante_id === producto.variante_id);

            let esPredeterminado = (
                (!producto.talla || producto.talla === 'Única' || producto.talla === 'Unica' || producto.talla === 'Estándar' || producto.talla === 'Estandar' || producto.talla === 'N/A') &&
                (!producto.color || producto.color === 'Estándar' || producto.color === 'Estandar' || producto.color === 'Único' || producto.color === 'Unico' || producto.color === 'N/A')
            );

            let desc = esPredeterminado ? 'General' : `${producto.talla}/${producto.color}`;

            if (existe) {
                if (existe.cantidad + 1 > producto.stock_actual) {
                    Swal.fire('Stock Insuficiente', 'No hay más unidades.', 'warning'); return;
                }
                existe.cantidad++;
            } else {
                if (producto.stock_actual < 1) { Swal.fire('Agotado', 'Sin stock.', 'error'); return; }
                carrito.push({
                    variante_id: producto.variante_id,
                    nombre: producto.nombre,
                    descripcion: desc,
                    precio: parseFloat(producto.precio_venta),
                    cantidad: 1,
                    max_stock: producto.stock_actual
                });
            }
            actualizarCarritoUI();
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1000 });
            Toast.fire({ icon: 'success', title: 'Agregado' });
        };

        function actualizarCarritoUI() {
            carritoBody.innerHTML = '';
            let total = 0;
            carrito.forEach((item, index) => {
                let subtotal = item.precio * item.cantidad;
                total += subtotal;
                carritoBody.innerHTML += `
                <tr>
                    <td><div class="fw-bold small text-truncate" style="max-width:120px;">${item.nombre}</div><small class="text-muted" style="font-size:0.75em;">${item.descripcion}</small></td>
                    <td class="text-center"><input type="number" class="form-control form-control-sm text-center p-0" value="${item.cantidad}" min="1" max="${item.max_stock}" onchange="cambiarCantidad(${index}, this.value)"></td>
                    <td class="text-end small fw-bold">${G_MONEDA} ${formatCOP(subtotal)}</td>
                    <td class="text-center"><button class="btn btn-sm text-danger p-0" onclick="eliminarItem(${index})"><i class="bi bi-x"></i></button></td>
                </tr>`;
            });
            totalSpan.innerText = formatCOP(total);
        }

        window.cambiarCantidad = (index, val) => {
            val = parseInt(val);
            if (val < 1) val = 1;
            if (val > carrito[index].max_stock) { val = carrito[index].max_stock; Swal.fire('Stock', 'Límite alcanzado.', 'warning'); }
            carrito[index].cantidad = val;
            actualizarCarritoUI();
        }
        window.eliminarItem = (idx) => { carrito.splice(idx, 1); actualizarCarritoUI(); }
        window.limpiarCarrito = () => { carrito = []; actualizarCarritoUI(); }

        // --- COBRAR ---
        let totalVentaActual = 0;
        
        window.cobrar = () => {
            if (carrito.length === 0) { Swal.fire('Vacío', 'Agrega productos.', 'warning'); return; }
            
            totalVentaActual = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
            
            document.getElementById('cobroTotalTexto').innerText = (typeof G_MONEDA !== 'undefined' ? G_MONEDA : '$') + ' ' + formatCOP(totalVentaActual);
            document.getElementById('montoRecibido').value = '';
            document.getElementById('vueltoTexto').innerText = '0';
            document.getElementById('formaPagoCobro').value = 'efectivo';
            verificarFormaPago();

            let modalCobroInst = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCobro'));
            modalCobroInst.show();
        }

        window.verificarFormaPago = () => {
            const forma = document.getElementById('formaPagoCobro').value;
            const divMonto = document.getElementById('divMontoRecibido');
            if(forma === 'efectivo') {
                divMonto.style.display = 'block';
                setTimeout(() => document.getElementById('montoRecibido').focus(), 500);
            } else {
                divMonto.style.display = 'none';
            }
        }

        window.calcularVuelto = () => {
            const monto = parseFloat(document.getElementById('montoRecibido').value) || 0;
            const vuelto = monto - totalVentaActual;
            document.getElementById('vueltoTexto').innerText = vuelto >= 0 ? formatCOP(vuelto) : '0';
        }

        window.confirmarVentaExterna = () => {
            const formaPago = document.getElementById('formaPagoCobro').value;
            
            if(formaPago === 'efectivo') {
                const monto = parseFloat(document.getElementById('montoRecibido').value) || 0;
                if(monto < totalVentaActual) {
                    Swal.fire('Monto Inválido', 'El monto recibido es menor al total de la venta.', 'warning');
                    return;
                }
            }

            // Procesar la venta
            fetch(`${BASE_URL}/ventas/guardar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ carrito: carrito, total: totalVentaActual, cliente_id: clienteActual.id, forma_pago: formaPago })
            })
            .then(res => res.json())
            .then(data => {
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCobro')).hide();
                if (data.status) {
                    clienteActual = { id: 1, nombre: 'Público General' };
                    document.getElementById('nombreCliente').innerText = clienteActual.nombre;
                    Swal.fire({
                        title: '¡Venta Exitosa!', 
                        html: `Ticket #${data.id_venta}<br>Forma de Pago: <b>${formaPago.toUpperCase()}</b>`, 
                        icon: 'success',
                        showCancelButton: true, confirmButtonText: '🖨️ Imprimir', cancelButtonText: 'Cerrar'
                    }).then((res) => {
                        limpiarCarrito();
                        buscador.value = ''; buscador.focus(); grid.innerHTML = '';
                        if (res.isConfirmed) window.open(`${BASE_URL}/ventas/imprimir/${data.id_venta}`, '_blank', 'width=400,height=600');
                    });
                } else { Swal.fire('Error', data.message, 'error'); }
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>