<?php
if (!class_exists('Database')) require_once '../app/core/Database.php';
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->query("SELECT COUNT(*) FROM producto_variantes v JOIN productos p ON v.producto_id = p.id WHERE v.stock_actual <= 5 AND p.activo = 1");
$stock_bajo = $stmt->fetchColumn();

// Cargar variables globales de la empresa
$stmtEmpresa = $conn->query("SELECT * FROM empresa WHERE id = 1");
$empresaGlobal = $stmtEmpresa->fetch(PDO::FETCH_ASSOC);
$logoEmpresa = $empresaGlobal['logo'] ?? null;
$nombreEmpresa = $empresaGlobal['nombre'] ?? 'Sistema Moda';
$monedaEmpresa = $empresaGlobal['moneda'] ?? '$';
?>

<div class="offcanvas offcanvas-start bg-dark text-white d-lg-none" tabindex="-1" id="sidebarMenu"
    aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold" id="sidebarMenuLabel">
            <?php if ($logoEmpresa): ?>
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($logoEmpresa) ?>" alt="Logo"
                    style="max-height: 30px; margin-right: 5px; border-radius: 4px; background: #fff; padding: 2px;">
            <?php else: ?>
                <i class="bi bi-shop fs-5 me-2"></i>
            <?php endif; ?>
            <?= htmlspecialchars($nombreEmpresa) ?>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-3">
        <?php include 'menu_items.php'; ?>
    </div>
</div>

<div class="d-none d-lg-flex flex-column flex-shrink-0 p-3 text-white bg-dark" id="mainSidebar"
    style="width: 260px; height: 100vh; position: fixed; top: 0; left: 0; overflow-y: auto; z-index: 1000;">

    <a href="#" class="d-flex align-items-center mb-4 mt-2 me-md-auto text-white text-decoration-none px-2 text-center w-100 flex-column">
        <?php if ($logoEmpresa): ?>
            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($logoEmpresa) ?>" alt="Logo"
                class="mb-2 shadow-sm" style="max-height: 70px; border-radius: 8px; background: #fff; padding: 5px;">
        <?php else: ?>
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 50px; height: 50px;">
                <i class="bi bi-gem fs-3"></i>
            </div>
        <?php endif; ?>
        <span class="fs-5 fw-bold" style="font-family: 'Playfair Display', serif; letter-spacing: 0.5px;"><?= htmlspecialchars($nombreEmpresa) ?></span>
    </a>
    <hr class="mt-0 opacity-25">

    <?php include 'menu_items.php'; ?>
</div>

<!-- Variable global JS para la moneda y helper de formato COP -->
<script>
    const G_MONEDA = "<?= htmlspecialchars($monedaEmpresa) ?>";

    function formatCOP(monto) {
        if (monto === null || monto === undefined || monto === '') return '0';
        let num = Math.round(parseFloat(monto) || 0);
        return num.toLocaleString('de-DE');
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Encontrar el elemento activo en el menú (con clase .active, excluyendo los pill básicos si los hubiera, pero usualmente usan .active)
        const activeItem = document.querySelector('#mainSidebar .nav-link.active') || document.querySelector('.offcanvas-body .nav-link.active');
        
        if (activeItem) {
            // Ubicar el contenedor que tiene el scroll
            const sidebar = document.getElementById('mainSidebar');
            if (sidebar) {
                // Calcular si el item está muy por debajo de la vista y hacer scroll
                const itemOffset = activeItem.offsetTop;
                const sidebarHeight = sidebar.clientHeight;
                
                // Si el item está más allá de la mitad del menú, lo centramos un poco
                if (itemOffset > sidebarHeight / 2) {
                    sidebar.scrollTop = itemOffset - (sidebarHeight / 2.5);
                }
            }
        }

        // --- SISTEMA DE CARGA VISUAL AL NAVEGAR ---
        const navLinks = document.querySelectorAll('.boutique-menu .nav-link, .dropdown-item');
        const loaderOverlay = document.getElementById('pageLoaderOverlay');

        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Si es un link válido (no # ni prevent default previo)
                const href = this.getAttribute('href');
                const target = this.getAttribute('target');
                
                if (href && href !== '#' && target !== '_blank' && !e.defaultPrevented) {
                    e.preventDefault(); // Prevenir navegación inmediata
                    // Mostrar el overlay de carga
                    if(loaderOverlay) {
                        loaderOverlay.style.display = 'flex';
                    }
                    // Forzar que el navegador dibuje el loader antes de congelar la pestaña navegando
                    setTimeout(() => {
                        window.location.href = href;
                    }, 50);
                }
            });
        });
        
        // También para el cambio de sucursal
        const selectsSucursal = document.querySelectorAll('.select-cambiar-sucursal');
        selectsSucursal.forEach(select => {
            select.addEventListener('change', function() {
                if(loaderOverlay) loaderOverlay.style.display = 'flex';
                // Delegar el submit al form padre después de pintar el loader
                setTimeout(() => {
                    this.form.submit();
                }, 50);
            });
        });
    });
</script>

<!-- Overlay de Carga Global -->
<style>
    #pageLoaderOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }
    .loader-spinner {
        width: 3rem;
        height: 3rem;
        border: 4px solid rgba(201, 132, 122, 0.3); /* Base de primary boutique */
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 1s infinite linear;
        margin-bottom: 1rem;
    }
    @keyframes spin { 100% { transform: rotate(360deg); } }
</style>

<div id="pageLoaderOverlay">
    <div class="loader-spinner"></div>
    <div class="fw-bold text-dark text-uppercase tracking-wide" style="letter-spacing: 2px;">Cargando...</div>
</div>