<?php 
$current_url = $_SERVER['REQUEST_URI'] ?? '';

// Estado de licencia para el ítem del menú
if (!class_exists('License')) {
    require_once '../app/core/License.php';
}
$_licValida = License::isValid();
$_licInfo   = License::getInfo();
$_licDiasRestantes = null;
if ($_licInfo && isset($_licInfo['fecha_fin'])) {
    $_licHoy = new DateTime('today');
    $_licFin = new DateTime($_licInfo['fecha_fin']);
    $_licDiasRestantes = (int)$_licHoy->diff($_licFin)->format('%r%a');
}
?>
<ul class="nav nav-pills flex-column mb-auto boutique-menu">
    <?php if(isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 'admin'): ?>
    <!-- Selector de Sucursal Top -->
    <li class="nav-item mb-3">
        <div class="p-2 rounded bg-opacity-10 bg-white border border-secondary border-opacity-25" style="box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
            <div class="d-flex align-items-center mb-1 text-white-50" style="font-size: 0.75rem;">
                <i class="bi bi-shop me-1"></i> SUCURSAL ACTIVA
            </div>
            <form action="<?= BASE_URL ?>/sucursal/cambiarActiva" method="POST" class="m-0 form-cambiar-sucursal">
                <select class="form-select form-select-sm bg-dark text-white fw-bold shadow-none select-cambiar-sucursal" name="sucursal_id" style="border-color: rgba(255,255,255,0.2);">
                    <?php 
                    if (!class_exists('Sucursal')) require_once '../app/models/Sucursal.php';
                    $sucModel = new Sucursal();
                    foreach($sucModel->listarActivas() as $suc): ?>
                        <option value="<?= $suc['id'] ?>" <?= (isset($_SESSION['sucursal_id']) && $_SESSION['sucursal_id'] == $suc['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($suc['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </li>
    <?php else: ?>
    <!-- Indicador de Sucursal Top -->
    <li class="nav-item mb-3">
        <div class="p-2 rounded bg-opacity-10 bg-white border border-secondary border-opacity-25 d-flex flex-column align-items-center text-center">
            <span class="text-white-50" style="font-size: 0.75rem;"><i class="bi bi-shop me-1"></i> SECTOR ACTUAL</span>
            <div class="fw-bold fs-6 text-white mt-1"><?= htmlspecialchars($_SESSION['sucursal_nombre'] ?? 'Sede Principal') ?></div>
        </div>
    </li>
    <?php endif; ?>

    <li class="nav-item mb-1"><a href="<?= BASE_URL ?>/home/index" class="nav-link <?= strpos($current_url, '/home') !== false ? 'active' : '' ?>"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
    
    <li class="nav-item mb-1"><a href="<?= BASE_URL ?>/ventas" class="nav-link text-white shadow <?= strpos($current_url, '/ventas') !== false && strpos($current_url, '/ventas/historial') === false ? 'active-tpv' : '' ?>" style="background-color: var(--success);"><i class="bi bi-cart4 me-2"></i> IR A VENDER (TPV)</a></li>
    <li class="nav-item mb-1"><a href="<?= BASE_URL ?>/ventas/historial" class="nav-link <?= strpos($current_url, '/ventas/historial') !== false ? 'active' : '' ?>"><i class="bi bi-clock-history me-2"></i> Historial Ventas</a></li>
    <li class="nav-item mb-1"><a href="<?= BASE_URL ?>/gasto/index" class="nav-link <?= strpos($current_url, '/gasto') !== false ? 'active' : '' ?>"><i class="bi bi-cash-coin me-2"></i> Gastos / Salidas</a></li>
    <li class="nav-item mb-3"><a href="<?= BASE_URL ?>/caja/index" class="nav-link <?= strpos($current_url, '/caja') !== false ? 'active' : '' ?>" style="color: #ffb4a6;"><i class="bi bi-wallet2 me-2"></i> Cerrar Caja</a></li>

    <div class="text-uppercase text-muted fw-bold small mb-2 mt-3" style="font-size: 11px; letter-spacing: 1px;">Gestión</div>

    <li class="nav-item">
        <a href="<?= BASE_URL ?>/producto/index" class="nav-link d-flex justify-content-between align-items-center <?= strpos($current_url, '/producto') !== false && strpos($current_url, '/producto/crear') === false ? 'active' : '' ?>">
            <div><i class="bi bi-box-seam me-2"></i> Inventario</div>
            <?php if(isset($stock_bajo) && $stock_bajo > 0): ?><span class="badge bg-danger rounded-pill"><?= $stock_bajo ?></span><?php endif; ?>
        </a>
    </li>
    
    <?php if(isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 'admin'): ?>
        <li><a href="<?= BASE_URL ?>/categoria/index" class="nav-link <?= strpos($current_url, '/categoria') !== false ? 'active' : '' ?>"><i class="bi bi-tags me-2"></i> Categorías</a></li>
    <?php endif; ?>

    <li><a href="<?= BASE_URL ?>/etiqueta/index" class="nav-link <?= strpos($current_url, '/etiqueta') !== false ? 'active' : '' ?>"><i class="bi bi-upc-scan me-2"></i> Códigos de Barra</a></li>
    <li><a href="<?= BASE_URL ?>/movimiento/index" class="nav-link <?= strpos($current_url, '/movimiento') !== false ? 'active' : '' ?>"><i class="bi bi-arrow-left-right me-2"></i> Mermas</a></li>
    <li><a href="<?= BASE_URL ?>/cliente/index" class="nav-link <?= strpos($current_url, '/cliente') !== false ? 'active' : '' ?>"><i class="bi bi-people me-2"></i> Clientes</a></li>
    <li><a href="<?= BASE_URL ?>/reporte/index" class="nav-link <?= strpos($current_url, '/reporte') !== false ? 'active' : '' ?>"><i class="bi bi-graph-up text-primary me-2"></i> Reportes Generales</a></li>

    <?php if(isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 'admin'): ?>
        <li class="mt-3 border-top pt-2">
            <div class="text-uppercase text-muted fw-bold small mb-2" style="font-size: 11px; letter-spacing: 1px;">Administración</div>
            <a href="<?= BASE_URL ?>/sucursal/index" class="nav-link <?= strpos($current_url, '/sucursal') !== false ? 'active' : '' ?>"><i class="bi bi-shop me-2"></i> Sucursales</a>
            <a href="<?= BASE_URL ?>/proveedor/index" class="nav-link <?= strpos($current_url, '/proveedor') !== false ? 'active' : '' ?>"><i class="bi bi-truck me-2"></i> Proveedores</a>
            <a href="<?= BASE_URL ?>/compra/crear" class="nav-link fw-bold <?= strpos($current_url, '/compra') !== false ? 'active' : '' ?>" style="color: var(--info);"><i class="bi bi-cart-plus-fill me-2"></i> Ingresar Compra</a>
            <a href="<?= BASE_URL ?>/usuario/index" class="nav-link <?= strpos($current_url, '/usuario') !== false ? 'active' : '' ?>"><i class="bi bi-people-fill me-2"></i> Equipo / Usuarios</a>
            <a href="<?= BASE_URL ?>/config/index" class="nav-link <?= strpos($current_url, '/config') !== false ? 'active' : '' ?>"><i class="bi bi-gear-fill me-2"></i> Configuración</a>
            <a href="<?= BASE_URL ?>/license/panel" class="nav-link d-flex align-items-center <?= strpos($current_url, '/license/panel') !== false ? 'active' : '' ?>" style="<?= !$_licValida ? 'color:#fca5a5;' : '' ?>">
                <i class="bi bi-shield-lock<?= !$_licValida ? '-fill' : '' ?> me-2"></i>
                <span>Mi Licencia</span>
                <?php if (!$_licValida): ?>
                    <span class="badge bg-danger ms-auto" style="font-size:.65rem;">!</span>
                <?php elseif ($_licDiasRestantes !== null && $_licDiasRestantes <= 15 && $_licDiasRestantes >= 0): ?>
                    <span class="badge bg-warning text-dark ms-auto" style="font-size:.65rem;"><?= $_licDiasRestantes ?>d</span>
                <?php endif; ?>
            </a>
        </li>
    <?php endif; ?>
</ul>

<hr>

<div class="dropdown dropup">
    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="rounded-circle d-flex justify-content-center align-items-center me-2 text-uppercase fw-bold" style="width: 32px; height: 32px; background-color: var(--primary); color: white;">
            <?= substr($_SESSION['user_nombre'] ?? 'U', 0, 1) ?>
        </div>
        <strong><?= $_SESSION['user_nombre'] ?? 'Usuario' ?></strong>
    </a>
    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
        <li><a class="dropdown-item" href="<?= BASE_URL ?>/perfil/index"><i class="bi bi-person-gear me-2"></i> Mi Perfil</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/auth/logout"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
    </ul>
</div>