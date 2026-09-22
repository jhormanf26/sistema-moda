<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($empresa['nombre'] ?? 'Variedades - Miscelánea & Bazar') ?> | Tienda Oficial</title>
    <meta name="description" content="Tienda en línea de <?= htmlspecialchars($empresa['nombre'] ?? 'Variedades') ?>. Papelería, oficina, ferretería, hogar, electrónica, snacks y más con entrega rápida.">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        brand: {
                            navy: "#0F172A",
                            orange: "#F97316",
                            coral: "#fd651e",
                            green: "#10B981",
                            sky: "#38BDF8",
                            lightBg: "#f8f9ff"
                        },
                        "primary": "#0F172A",
                        "secondary": "#F97316",
                        "secondary-container": "#ea580c",
                        "on-surface": "#0d1c2f",
                        "on-surface-variant": "#45464d",
                        "surface-container-low": "#eff4ff",
                        "surface-container-lowest": "#ffffff"
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        headline: ['Plus Jakarta Sans', 'sans-serif']
                    }
                }
            }
        };
    </script>
    <style>
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .glass-header { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); }
    </style>
</head>
<body class="bg-[#f8f9ff] font-sans text-[#0d1c2f] antialiased min-h-screen flex flex-col justify-between selection:bg-[#F97316]/20 selection:text-[#F97316] max-w-full overflow-x-hidden">



    <!-- HEADER DE LA TIENDA -->
    <header class="sticky top-0 z-40 w-full glass-header border-b border-slate-200/80 shadow-sm max-w-full overflow-hidden">
        
        <!-- Ticker de Anuncio y Contacto -->
        <div class="bg-slate-900 text-white text-[11px] sm:text-xs py-1.5 px-3 sm:px-8 overflow-hidden">
            <div class="max-w-[1440px] mx-auto flex items-center justify-between gap-2 min-w-0">
                <div class="flex items-center gap-1.5 min-w-0 truncate">
                    <span class="bg-[#F97316] text-white font-bold text-[9px] uppercase px-1.5 py-0.5 rounded flex-shrink-0">Aviso</span>
                    <span class="truncate text-slate-200">
                        Pedidos WhatsApp: <strong class="text-amber-400"><?= htmlspecialchars($empresa['telefono'] ?? '+52 55 1234 5678') ?></strong>
                    </span>
                </div>
                <div class="hidden md:flex items-center gap-4 text-slate-300 flex-shrink-0">
                    <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Horario: Lun - Sáb (8:00 AM - 9:00 PM)</span>
                    <span>|</span>
                    <a href="#sucursales" class="hover:text-white transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px] text-[#F97316]">location_on</span>
                        <span>Ver Sucursales</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Barra Principal de Navegación y Búsqueda -->
        <div class="max-w-[1440px] mx-auto px-3 sm:px-8 py-2.5 flex items-center justify-between gap-2 min-w-0">
            <!-- Logo Marca -->
            <a href="<?= BASE_URL ?>/tienda" class="flex items-center gap-2 group min-w-0 flex-1">
                <?php if (!empty($empresa['logo']) && is_file('../public/' . ltrim($empresa['logo'], '/'))): ?>
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars(ltrim($empresa['logo'], '/')) ?>" alt="Logo" class="h-8 sm:h-10 w-auto object-contain flex-shrink-0">
                <?php else: ?>
                    <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-slate-900 flex items-center justify-center text-[#F97316] shadow-md flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                    </div>
                <?php endif; ?>
                <div class="flex flex-col min-w-0 truncate">
                    <span class="font-headline font-extrabold text-sm sm:text-xl text-slate-900 leading-tight tracking-tight uppercase truncate">
                        <?= htmlspecialchars($empresa['nombre'] ?? 'VARIEDADES') ?>
                    </span>
                    <span class="font-headline font-bold text-[9px] sm:text-[10px] text-[#F97316] tracking-widest uppercase -mt-0.5 truncate">
                        BEAUTY & ACCESSORIES
                    </span>
                </div>
            </a>

            <!-- Formulario de Búsqueda (Escritorio) -->
            <div class="flex-1 max-w-2xl hidden md:block">
                <form action="<?= BASE_URL ?>/tienda" method="GET" class="flex items-center h-11 bg-slate-100 rounded-xl border border-slate-200 focus-within:border-[#F97316] focus-within:bg-white focus-within:ring-2 focus-within:ring-[#F97316]/20 transition-all overflow-hidden">
                    <div class="relative flex items-center px-3 bg-slate-200/60 h-full text-xs font-semibold text-slate-700">
                        <span class="material-symbols-outlined text-[18px] mr-1 text-slate-500">category</span>
                        <select name="cat" onchange="this.form.submit()" class="bg-transparent focus:outline-none cursor-pointer text-slate-800 pr-2">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (isset($_GET['cat']) && $_GET['cat'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="h-6 w-px bg-slate-300"></div>
                    <div class="relative flex-1 flex items-center h-full">
                        <span class="material-symbols-outlined absolute left-3 text-slate-400 text-[20px]">search</span>
                        <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Buscar maquillaje, skincare, labiales, joyería, accesorios..." class="w-full h-full pl-10 pr-3 bg-transparent text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none">
                    </div>
                    <button type="submit" class="h-full px-5 bg-[#F97316] hover:bg-[#ea580c] text-white font-bold text-sm flex items-center gap-1 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">search</span>
                        <span>Buscar</span>
                    </button>
                </form>
            </div>

            <!-- Acciones Header (Carrito / Login) -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="<?= BASE_URL ?>/auth/index" class="hidden sm:flex items-center gap-1 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-[#F97316] hover:bg-slate-100 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[20px]">lock</span>
                        <span>Acceso Admin</span>
                    </a>
                <?php endif; ?>

                <!-- Botón Carrito de Compras -->
                <button onclick="toggleCartDrawer()" class="flex items-center gap-2 bg-orange-50 hover:bg-orange-100 border border-orange-200/80 px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-xl transition-all shadow-sm group">
                    <div class="relative">
                        <span class="material-symbols-outlined text-slate-800 group-hover:scale-110 transition-transform text-[22px] sm:text-[24px]">shopping_bag</span>
                        <span id="cart-badge-count" class="absolute -top-2 -right-2 bg-[#F97316] text-white text-[10px] font-bold w-4 h-4 sm:w-5 sm:h-5 rounded-full flex items-center justify-center shadow-sm">0</span>
                    </div>
                    <div class="hidden sm:flex flex-col text-left leading-tight">
                        <span class="text-[11px] font-medium text-slate-500">Mi Carrito</span>
                        <span id="cart-badge-total" class="font-headline font-extrabold text-sm text-slate-900"><?= $empresa['moneda'] ?? 'S/' ?> 0.00</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Categorías Navbar -->
        <nav class="bg-white border-t border-slate-100 overflow-x-auto max-w-full">
            <div class="max-w-[1440px] mx-auto px-3 sm:px-8 flex items-center gap-1 py-1.5 whitespace-nowrap text-xs font-semibold text-slate-600">
                <a href="<?= BASE_URL ?>/tienda" class="px-3 py-1.5 rounded-lg hover:bg-slate-100 <?= empty($_GET['cat']) ? 'bg-slate-900 text-white font-bold' : '' ?> transition-colors">
                    Todos los productos
                </a>
                <?php foreach ($categorias as $cat): ?>
                    <a href="<?= BASE_URL ?>/tienda?cat=<?= $cat['id'] ?>" class="px-3 py-1.5 rounded-lg hover:bg-slate-100 hover:text-[#F97316] <?= (isset($_GET['cat']) && $_GET['cat'] == $cat['id']) ? 'bg-[#F97316] text-white font-bold' : '' ?> transition-colors">
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </nav>

        <!-- Buscador Responsivo Móvil -->
        <div class="px-3 py-2 bg-slate-50 border-t border-slate-100 md:hidden">
            <form action="<?= BASE_URL ?>/tienda" method="GET" class="flex items-center h-10 bg-white rounded-xl border border-slate-200 focus-within:border-[#F97316] shadow-xs px-3 gap-2">
                <span class="material-symbols-outlined text-slate-400 text-[18px]">search</span>
                <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Buscar productos..." class="w-full bg-transparent text-xs text-slate-800 focus:outline-none">
                <button type="submit" class="text-[#F97316] font-bold text-xs px-1">Buscar</button>
            </form>
        </div>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="max-w-[1440px] w-full mx-auto px-4 sm:px-8 py-6 space-y-8 flex-1">

        <!-- 1. HERO BENTO BANNER (BEAUTY & ACCESSORIES) -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            <!-- Banner Principal -->
            <div class="lg:col-span-8 bg-gradient-to-br from-slate-950 via-rose-950/40 to-slate-900 text-white rounded-3xl p-6 sm:p-10 flex flex-col justify-between shadow-xl relative overflow-hidden group border border-rose-900/30">
                <!-- SVG Grid Backdrop -->
                <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#f43f5e_1px,transparent_1px)] [background-size:20px_20px]"></div>
                <div class="absolute -right-16 -bottom-16 w-80 h-80 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="relative z-10 space-y-4 max-w-xl">
                    <span class="inline-flex items-center gap-1.5 bg-rose-500/20 border border-rose-400/40 text-rose-300 text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                        <span class="material-symbols-outlined text-[16px]">auto_awesome</span>
                        Beauty & Accesorios de Tendencia
                    </span>
                    <h1 class="font-headline font-extrabold text-3xl sm:text-5xl leading-tight tracking-tight text-white">
                        Resalta tu <span class="text-rose-400 underline decoration-amber-300 decoration-4 underline-offset-4">belleza & estilo</span> único cada día
                    </h1>
                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                        Cuidado personal, cosmética, skincare y accesorios de moda seleccionados para hacer lucir tu mejor versión. Envíos exprés directos a tu puerta o retiro en tienda.
                    </p>
                </div>

                <div class="relative z-10 pt-8 flex flex-wrap items-center gap-4">
                    <a href="#catalogo" class="bg-rose-600 hover:bg-rose-700 text-white font-headline font-bold text-sm px-6 py-3.5 rounded-xl shadow-lg hover:shadow-rose-600/30 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">sparkles</span>
                        <span>Explorar Colección Beauty</span>
                    </a>
                    <a href="https://api.whatsapp.com/send?phone=<?= preg_replace('/[^0-9]/', '', $empresa['telefono'] ?? '525512345678') ?>&text=Hola,%20quisiera%20consultar%20por%20sus%20productos%20de%20Beauty%20%26%20Accessories" target="_blank" class="bg-white/10 hover:bg-white/20 border border-white/20 text-white font-headline font-semibold text-sm px-6 py-3.5 rounded-xl backdrop-blur transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-emerald-400">chat</span>
                        <span>Pedir por WhatsApp</span>
                    </a>
                </div>
            </div>

            <!-- Promos Laterales Beauty -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                <!-- Card Promo 1: Maquillaje & Skincare -->
                <div class="bg-rose-50/80 border border-rose-200/80 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
                    <div class="space-y-2 relative z-10">
                        <span class="bg-rose-900 text-white font-bold text-[10px] uppercase px-2.5 py-0.5 rounded">Tendencia Beauty</span>
                        <h3 class="font-headline font-bold text-slate-900 text-lg group-hover:text-rose-600 transition-colors">
                            Maquillaje & Skincare
                        </h3>
                        <p class="text-slate-600 text-xs">
                            Paletas de sombras, labiales, sueros faciales, cosmetiqueras y cuidado personal.
                        </p>
                    </div>
                    <div class="pt-4 flex items-center justify-between relative z-10">
                        <a href="#catalogo" class="font-bold text-xs text-rose-600 hover:underline flex items-center gap-1">
                            Ver Maquillaje <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>
                        <span class="w-9 h-9 rounded-full bg-white shadow-sm flex items-center justify-center text-rose-600">
                            <span class="material-symbols-outlined text-[20px]">auto_awesome</span>
                        </span>
                    </div>
                </div>

                <!-- Card Promo 2: Accesorios & Joyería -->
                <div class="bg-purple-50/80 border border-purple-200/80 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
                    <div class="space-y-2 relative z-10">
                        <span class="bg-purple-900 text-white font-bold text-[10px] uppercase px-2.5 py-0.5 rounded">Moda & Estilo</span>
                        <h3 class="font-headline font-bold text-slate-900 text-lg group-hover:text-purple-700 transition-colors">
                            Joyería & Accesorios
                        </h3>
                        <p class="text-slate-600 text-xs">
                            Cadenas, aretes, scrunchies, monederos y complementos de moda.
                        </p>
                    </div>
                    <div class="pt-4 flex items-center justify-between relative z-10">
                        <a href="#catalogo" class="font-bold text-xs text-purple-700 hover:underline flex items-center gap-1">
                            Ver Accesorios <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>
                        <span class="w-9 h-9 rounded-full bg-white shadow-sm flex items-center justify-center text-[#F97316]">
                            <span class="material-symbols-outlined text-[20px]">diamond</span>
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. BARRA DE BENEFICIOS / VALORES BEAUTY -->
        <section class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200/80 shadow-sm grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex items-center gap-3 p-2">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[22px]">local_shipping</span>
                </div>
                <div>
                    <div class="font-headline font-bold text-xs text-slate-900">Envíos Rápidos</div>
                    <div class="text-[11px] text-slate-500">Entrega directa a tu puerta</div>
                </div>
            </div>
            <div class="flex items-center gap-3 p-2">
                <div class="w-10 h-10 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[22px]">verified</span>
                </div>
                <div>
                    <div class="font-headline font-bold text-xs text-slate-900">Calidad Garantizada</div>
                    <div class="text-[11px] text-slate-500">Productos para tu piel y estilo</div>
                </div>
            </div>
            <div class="flex items-center gap-3 p-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[22px]">payments</span>
                </div>
                <div>
                    <div class="font-headline font-bold text-xs text-slate-900">Pago Flexible</div>
                    <div class="text-[11px] text-slate-500">Contra entrega, Yape/Plin o tarjeta</div>
                </div>
            </div>
            <div class="flex items-center gap-3 p-2">
                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[22px]">support_agent</span>
                </div>
                <div>
                    <div class="font-headline font-bold text-xs text-slate-900">Asesoría WhatsApp</div>
                    <div class="text-[11px] text-slate-500">Atención personalizada directa</div>
                </div>
            </div>
        </section>

        <!-- 3. REJILLA DE CATEGORÍAS RÁPIDAS -->
        <section class="space-y-4">
            <div class="flex items-end justify-between">
                <div>
                    <span class="text-xs font-bold text-[#F97316] uppercase tracking-wider">Explora el catálogo</span>
                    <h2 class="font-headline font-extrabold text-xl sm:text-2xl text-slate-900">Categorías Destacadas</h2>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php foreach ($categorias as $index => $cat): ?>
                    <a href="<?= BASE_URL ?>/tienda?cat=<?= $cat['id'] ?>" class="bg-white hover:bg-orange-50/50 border border-slate-200/80 hover:border-orange-300 rounded-2xl p-4 text-center shadow-sm hover:shadow-md transition-all flex flex-col items-center group">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-800 group-hover:bg-[#F97316] group-hover:text-white transition-all flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-[28px]">
                                <?php 
                                    $icons = ['category', 'edit_note', 'build', 'cable', 'home', 'cookie', 'soap', 'style', 'local_mall'];
                                    echo $icons[$index % count($icons)];
                                ?>
                            </span>
                        </div>
                        <span class="font-headline font-bold text-xs text-slate-800 group-hover:text-[#F97316] transition-colors line-clamp-1">
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </span>
                        <span class="text-[11px] text-slate-500 mt-0.5"><?= $cat['total_productos'] ?? 0 ?> productos</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 3.5 SECCIÓN DE COMBOS & PACKS PROMOCIONALES -->
        <?php if (!empty($combos)): ?>
        <section id="combos" class="space-y-4 pt-2">
            <div class="flex items-end justify-between border-b border-rose-100 pb-3">
                <div>
                    <span class="text-xs font-bold text-rose-600 uppercase tracking-wider flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">auto_awesome</span> Ahorro & Ofertas Especiales
                    </span>
                    <h2 class="font-headline font-extrabold text-xl sm:text-2xl text-slate-900">Combos & Packs Promocionales 🎁</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($combos as $combo): ?>
                    <div class="bg-white border border-slate-200/90 rounded-3xl p-5 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between relative overflow-hidden group">
                        
                        <!-- Header & Badges -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="bg-rose-50 border border-rose-200 text-rose-700 font-headline font-bold text-[10px] uppercase px-2.5 py-1 rounded-full flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">stars</span> Pack Promocional
                                </span>
                                <?php if ($combo['estado_stock'] === 'disponible'): ?>
                                    <span class="bg-emerald-100 text-emerald-700 font-bold text-[10px] uppercase px-2.5 py-1 rounded-full flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disponible
                                    </span>
                                <?php else: ?>
                                    <span class="bg-rose-100 text-rose-700 font-bold text-[10px] uppercase px-2.5 py-1 rounded-full flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Agotado
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Imagen del Combo -->
                            <div class="w-full h-48 rounded-2xl bg-gradient-to-br from-rose-50 to-slate-50 border border-slate-100 overflow-hidden relative flex items-center justify-center">
                                <?php if (!empty($combo['imagen'])): ?>
                                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($combo['imagen']) ?>" alt="<?= htmlspecialchars($combo['nombre']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <?php else: ?>
                                    <div class="text-center p-4">
                                        <span class="material-symbols-outlined text-5xl text-rose-400 mb-1">card_giftcard</span>
                                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pack Especial</div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Nombre y Descripción -->
                            <div>
                                <h3 class="font-headline font-bold text-lg text-slate-900 group-hover:text-rose-600 transition-colors line-clamp-1">
                                    <?= htmlspecialchars($combo['nombre']) ?>
                                </h3>
                                <p class="text-xs text-slate-500 line-clamp-2 mt-1">
                                    <?= htmlspecialchars($combo['descripcion'] ?? 'Aprovecha este pack promocional con productos seleccionados.') ?>
                                </p>
                            </div>

                            <!-- Desglose de Productos Incluidos -->
                            <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 space-y-1.5">
                                <div class="text-[11px] font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px] text-rose-500">checklist</span>
                                    Incluye <?= count($combo['items']) ?> producto(s):
                                </div>
                                <ul class="space-y-1">
                                    <?php foreach ($combo['items'] as $it): ?>
                                        <li class="text-xs text-slate-700 flex items-center justify-between">
                                            <span class="truncate pr-2">• <strong><?= $it['cantidad'] ?>x</strong> <?= htmlspecialchars($it['producto_nombre']) ?></span>
                                            <?php if ($it['stock_total_producto'] < $it['cantidad']): ?>
                                                <span class="text-[10px] font-bold text-rose-500 flex-shrink-0">(Sin stock)</span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <!-- Footer Precio y Acción -->
                        <div class="pt-4 border-t border-slate-100 mt-4 flex items-center justify-between gap-3">
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Precio Especial</div>
                                <div class="flex items-baseline gap-1.5">
                                    <span class="font-headline font-extrabold text-2xl text-rose-600">
                                        <?= $empresa['moneda'] ?? '$' ?> <?= number_format($combo['precio'], 2) ?>
                                    </span>
                                    <?php if ($combo['ahorro'] > 0): ?>
                                        <span class="text-xs text-slate-400 line-through">
                                            <?= $empresa['moneda'] ?? '$' ?> <?= number_format($combo['precio_original_total'], 2) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <button onclick='agregarComboAlCarrito(<?= json_encode($combo) ?>)' 
                                    <?= $combo['estado_stock'] === 'agotado' ? 'disabled' : '' ?>
                                    class="bg-rose-600 hover:bg-rose-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-headline font-bold text-xs px-4 py-2.5 rounded-xl flex items-center gap-1.5 shadow-md active:scale-95 transition-all">
                                <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                <span><?= $combo['estado_stock'] === 'agotado' ? 'Agotado' : 'Agregar Combo' ?></span>
                            </button>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- 4. SECCIÓN CATÁLOGO DE PRODUCTOS -->
        <section id="catalogo" class="space-y-6 pt-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
                <div>
                    <span class="text-xs font-bold text-[#F97316] uppercase tracking-wider">Productos Disponibles</span>
                    <h2 class="font-headline font-extrabold text-2xl text-slate-900">
                        <?= !empty($_GET['cat']) ? 'Filtrado por Categoría' : 'Catálogo General' ?>
                    </h2>
                </div>

                <!-- Buscador rápido en vivo -->
                <div class="relative w-full sm:w-72">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-[18px]">search</span>
                    <input type="text" id="live-search" placeholder="Filtrar productos..." onkeyup="filtrarProductosEnVivo()" class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-[#F97316] shadow-sm">
                </div>
            </div>

            <!-- Grid de Productos -->
            <?php if (empty($productos)): ?>
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 space-y-4">
                    <span class="material-symbols-outlined text-6xl text-slate-300">inventory_2</span>
                    <h3 class="font-headline font-bold text-lg text-slate-800">No se encontraron productos</h3>
                    <p class="text-xs text-slate-500">Prueba cambiando la búsqueda o seleccionando otra categoría.</p>
                    <a href="<?= BASE_URL ?>/tienda" class="inline-block bg-slate-900 text-white text-xs font-bold px-4 py-2 rounded-xl">Ver todos los productos</a>
                </div>
            <?php else: ?>
                <div id="grid-productos" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
                    <?php foreach ($productos as $p): ?>
                        <?php 
                            // 1. Manejo seguro de imagen (Validación is_file para evitar cargar carpetas o directorios)
                            $imgPath = !empty($p['imagen']) ? ltrim($p['imagen'], '/') : '';
                            $imgRuta = (!empty($imgPath) && is_file('../public/' . $imgPath)) 
                                ? BASE_URL . '/' . htmlspecialchars($imgPath) 
                                : 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=500&q=80';

                            // 2. Lógica de variaciones de stock y etiquetas llamativas
                            $stock = intval($p['stock_total']);
                            if ($stock <= 0) {
                                $badgeTop = '<span class="absolute top-2 left-2 z-10 bg-rose-600 text-white font-extrabold text-[9px] sm:text-[10px] px-2 py-0.5 rounded-full shadow uppercase">Agotado</span>';
                                $stockLabel = '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold bg-rose-100 text-rose-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Agotado
                                               </span>';
                            } else if ($stock <= 10) {
                                $badgeTop = '<span class="absolute top-2 left-2 z-10 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-extrabold text-[9px] sm:text-[10px] px-2 py-0.5 rounded-full shadow uppercase flex items-center gap-0.5">
                                                <span class="material-symbols-outlined text-[11px] sm:text-[13px]">local_fire_department</span> Pocas Unidades
                                             </span>';
                                $stockLabel = '<span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                                                <span class="material-symbols-outlined text-[12px] sm:text-[14px] text-amber-600">local_fire_department</span> ¡Últimas ' . $stock . ' unid!
                                               </span>';
                            } else {
                                $badgeTop = '<span class="absolute top-2 left-2 z-10 bg-slate-900/90 text-white font-bold text-[9px] sm:text-[10px] px-2 py-0.5 rounded-md backdrop-blur truncate max-w-[80%]">' . htmlspecialchars($p['categoria_nombre'] ?? 'General') . '</span>';
                                $stockLabel = '<span class="inline-flex items-center gap-1 text-emerald-600 font-semibold text-[10px] sm:text-[11px]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Stock: ' . $stock . ' unid.
                                               </span>';
                            }
                        ?>
                        <div class="producto-card bg-white rounded-2xl border border-slate-200/80 p-2.5 sm:p-4 flex flex-col justify-between shadow-sm hover:shadow-xl transition-all duration-300 group" 
                             data-nombre="<?= strtolower(htmlspecialchars($p['nombre'])) ?>" 
                             data-categoria="<?= strtolower(htmlspecialchars($p['categoria_nombre'] ?? '')) ?>">
                            
                            <div>
                                <!-- Contenedor de Imagen -->
                                <div class="relative w-full aspect-square rounded-xl bg-slate-100 overflow-hidden mb-3">
                                    <?= $badgeTop ?>
                                    
                                    <!-- Botón Vista Rápida -->
                                    <button onclick="abrirQuickView(<?= $p['id'] ?>)" class="absolute top-2.5 right-2.5 z-10 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-slate-700 flex items-center justify-center shadow-md transition-all hover:scale-110" title="Vista Rápida">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </button>

                                    <!-- Imagen del producto con fallback seguro contra fallas -->
                                    <img src="<?= $imgRuta ?>" 
                                         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=500&q=80';"
                                         alt="<?= htmlspecialchars($p['nombre']) ?>" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>

                                <!-- Calificación Estrellas -->
                                <div class="flex items-center gap-1 text-amber-400 text-xs mb-1">
                                    <span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1;">star_half</span>
                                    <span class="text-[11px] text-slate-400 ml-1 font-medium">(4.8)</span>
                                </div>

                                <!-- Nombre del Producto -->
                                <h3 class="font-headline font-semibold text-slate-900 text-sm group-hover:text-[#F97316] transition-colors line-clamp-2 leading-snug">
                                    <?= htmlspecialchars($p['nombre']) ?>
                                </h3>

                                <!-- Indicador de Stock Llamativo -->
                                <div class="mt-2.5 flex items-center justify-between">
                                    <?= $stockLabel ?>
                                </div>

                                <!-- Precio -->
                                <div class="mt-3 flex items-baseline gap-2">
                                    <span class="font-headline font-extrabold text-xl text-[#F97316]">
                                        <?= $empresa['moneda'] ?? 'S/' ?> <?= number_format($p['precio_venta'], 2) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="pt-4 flex gap-2">
                                <?php if ($stock > 0): ?>
                                    <button onclick="agregarAlCarrito(<?= htmlspecialchars(json_encode($p)) ?>)" class="flex-1 bg-[#F97316] hover:bg-[#ea580c] text-white font-headline font-bold text-xs py-2.5 rounded-xl flex items-center justify-center gap-1.5 transition-all shadow-sm active:scale-95">
                                        <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                        <span>Agregar</span>
                                    </button>
                                <?php else: ?>
                                    <button disabled class="flex-1 bg-slate-200 text-slate-400 font-headline font-bold text-xs py-2.5 rounded-xl flex items-center justify-center gap-1 cursor-not-allowed">
                                        <span class="material-symbols-outlined text-[18px]">block</span>
                                        <span>Agotado</span>
                                    </button>
                                <?php endif; ?>
                                <button onclick="abrirQuickView(<?= $p['id'] ?>)" class="px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-colors">
                                    <span class="material-symbols-outlined text-[18px] align-middle">info</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-white mt-16 border-t border-slate-800">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-8 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-3">
                <h3 class="font-headline font-extrabold text-lg text-white uppercase tracking-tight">
                    <?= htmlspecialchars($empresa['nombre'] ?? 'VARIEDADES') ?>
                </h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    <?= htmlspecialchars($empresa['mensaje'] ?? 'Tu tienda de confianza con el mejor surtido en papelería, ferretería, hogar y productos para el día a día.') ?>
                </p>
            </div>

            <div class="space-y-3" id="sucursales">
                <h4 class="font-headline font-bold text-sm text-amber-400">Sucursal & Atención</h4>
                <ul class="text-xs text-slate-300 space-y-2">
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-[#F97316]">location_on</span>
                        <span><?= htmlspecialchars($empresa['direccion'] ?? 'Dirección Principal') ?></span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-[#F97316]">phone</span>
                        <span>Teléfono: <?= htmlspecialchars($empresa['telefono'] ?? '0000000') ?></span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-[#F97316]">mail</span>
                        <span><?= htmlspecialchars($empresa['email'] ?? 'contacto@tienda.com') ?></span>
                    </li>
                </ul>
            </div>

            <div class="space-y-3">
                <h4 class="font-headline font-bold text-sm text-amber-400">Enlaces Rápídos</h4>
                <ul class="text-xs text-slate-300 space-y-2">
                    <li><a href="<?= BASE_URL ?>/tienda" class="hover:text-white transition-colors">Catálogo de Productos</a></li>
                    <li><a href="<?= BASE_URL ?>/auth/index" class="hover:text-white transition-colors">Acceso Administrador</a></li>
                </ul>
            </div>

            <div class="space-y-3">
                <h4 class="font-headline font-bold text-sm text-amber-400">Métodos de Pago</h4>
                <div class="flex flex-wrap gap-2 text-xs text-slate-400">
                    <span class="bg-slate-800 px-3 py-1 rounded border border-slate-700">💵 Efectivo en tienda</span>
                    <span class="bg-slate-800 px-3 py-1 rounded border border-slate-700">📱 Yape / Plin</span>
                    <span class="bg-slate-800 px-3 py-1 rounded border border-slate-700">💳 Tarjeta Débito/Crédito</span>
                </div>
            </div>
        </div>
        <div class="bg-slate-950 py-4 text-center text-xs text-slate-500 border-t border-slate-800">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($empresa['nombre'] ?? 'Variedades') ?>. Todos los derechos reservados.
        </div>
    </footer>

    <!-- CART DRAWER (PANEL DESLIZANTE DE CARRITO) -->
    <div id="cart-drawer-backdrop" onclick="toggleCartDrawer()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden transition-opacity"></div>
    <aside id="cart-drawer" class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white z-50 shadow-2xl transform translate-x-full transition-transform duration-300 flex flex-col justify-between">
        <!-- Header del Carrito -->
        <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-slate-900 text-white">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#F97316]">shopping_bag</span>
                <h3 class="font-headline font-bold text-sm">Tu Carrito de Compras</h3>
            </div>
            <button onclick="toggleCartDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Lista de Items en Carrito -->
        <div id="cart-items-container" class="flex-1 overflow-y-auto p-4 space-y-4">
            <!-- Renderizado dinámico vía JavaScript -->
        </div>

        <!-- Footer del Carrito con Checkout -->
        <div class="p-4 border-t border-slate-200 bg-slate-50 space-y-3">
            <div class="flex justify-between items-center text-sm font-semibold text-slate-800">
                <span>Total a Pagar:</span>
                <span id="cart-drawer-total" class="font-headline font-extrabold text-lg text-[#F97316]"><?= $empresa['moneda'] ?? 'S/' ?> 0.00</span>
            </div>

            <button onclick="enviarPedidoWhatsapp()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-headline font-bold text-xs py-3 rounded-xl flex items-center justify-center gap-2 transition-colors shadow-md">
                <span class="material-symbols-outlined text-[18px]">chat</span>
                <span>Enviar Pedido por WhatsApp</span>
            </button>
        </div>
    </aside>

    <!-- QUICK VIEW MODAL (VISTA RÁPIDA DE PRODUCTO CON GALERÍA) -->
    <div id="quickview-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 relative overflow-hidden shadow-2xl max-h-[90vh] overflow-y-auto space-y-4">
            <button onclick="cerrarQuickView()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>

            <div id="quickview-content" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Carga dinámica vía JS -->
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LÓGICA DE TIENDA & CARRITO -->
    <script>
        const MONEDA = '<?= $empresa['moneda'] ?? "S/" ?>';
        const TELEFONO_WHATSAPP = '<?= preg_replace('/[^0-9]/', '', $empresa['telefono'] ?? "525512345678") ?>';
        const BASE_URL_APP = '<?= BASE_URL ?>';

        let carrito = JSON.parse(localStorage.getItem('tienda_carrito') || '[]');
        let varianteActual = null;
        let productoActualModal = null;

        function guardarCarrito() {
            localStorage.setItem('tienda_carrito', JSON.stringify(carrito));
            actualizarUI();
        }

        function agregarAlCarrito(producto, variante = null, cantidad = 1) {
            const varId = variante ? variante.id : (producto.variante_id || null);
            const varTexto = variante 
                ? [variante.talla ? 'Talla ' + variante.talla : '', variante.color || ''].filter(Boolean).join(' / ')
                : '';
            
            const key = varId ? `${producto.id}_var_${varId}` : `${producto.id}`;
            const nombreCompleto = varTexto ? `${producto.nombre} (${varTexto})` : producto.nombre;

            const index = carrito.findIndex(item => item.key === key);
            if (index > -1) {
                carrito[index].cantidad += cantidad;
            } else {
                carrito.push({
                    key: key,
                    id: producto.id,
                    variante_id: varId,
                    nombre: nombreCompleto,
                    precio: parseFloat(producto.precio_venta),
                    imagen: producto.imagen,
                    cantidad: cantidad
                });
            }
            guardarCarrito();
            toggleCartDrawer(true);
        }

        function agregarComboAlCarrito(combo) {
            if (combo.estado_stock === 'agotado') {
                alert('Este combo está agotado por falta de stock en uno de sus componentes.');
                return;
            }
            const key = `combo-${combo.id}`;
            const index = carrito.findIndex(i => i.key === key);
            if (index > -1) {
                carrito[index].cantidad += 1;
            } else {
                const descItems = combo.items ? combo.items.map(it => `${it.cantidad}x ${it.producto_nombre}`).join(', ') : '';
                carrito.push({
                    key: key,
                    id: combo.id,
                    nombre: `🎁 ${combo.nombre} (${descItems})`,
                    precio: parseFloat(combo.precio),
                    imagen: combo.imagen,
                    cantidad: 1,
                    esCombo: true
                });
            }
            guardarCarrito();
            toggleCartDrawer(true);
        }

        function cambiarCantidad(key, delta) {
            const index = carrito.findIndex(item => item.key === key || item.id === key);
            if (index > -1) {
                carrito[index].cantidad += delta;
                if (carrito[index].cantidad <= 0) {
                    carrito.splice(index, 1);
                }
            }
            guardarCarrito();
        }

        function actualizarUI() {
            const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);
            const totalMonto = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);

            document.getElementById('cart-badge-count').innerText = totalItems;
            document.getElementById('cart-badge-total').innerText = `${MONEDA} ${totalMonto.toFixed(2)}`;
            document.getElementById('cart-drawer-total').innerText = `${MONEDA} ${totalMonto.toFixed(2)}`;

            const container = document.getElementById('cart-items-container');
            if (carrito.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12 text-slate-400 space-y-2">
                        <span class="material-symbols-outlined text-4xl">shopping_cart</span>
                        <p class="text-xs font-semibold">Tu carrito está vacío</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = carrito.map(item => {
                const imgRuta = item.imagen ? `${BASE_URL_APP}/${item.imagen}` : 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=200&q=80';
                return `
                    <div class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200/80 rounded-xl">
                        <img src="${imgRuta}" class="w-14 h-14 object-cover rounded-lg bg-white border">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-semibold text-slate-800 truncate">${item.nombre}</h4>
                            <div class="text-xs text-[#F97316] font-bold">${MONEDA} ${item.precio.toFixed(2)}</div>
                            <div class="flex items-center gap-2 mt-1">
                                <button onclick="cambiarCantidad('${item.key}', -1)" class="w-5 h-5 bg-slate-200 rounded flex items-center justify-center text-xs font-bold">-</button>
                                <span class="text-xs font-bold">${item.cantidad}</span>
                                <button onclick="cambiarCantidad('${item.key}', 1)" class="w-5 h-5 bg-slate-200 rounded flex items-center justify-center text-xs font-bold">+</button>
                            </div>
                        </div>
                        <button onclick="cambiarCantidad('${item.key}', -${item.cantidad})" class="text-slate-400 hover:text-red-500">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                `;
            }).join('');
        }

        function toggleCartDrawer(forceOpen = false) {
            const drawer = document.getElementById('cart-drawer');
            const backdrop = document.getElementById('cart-drawer-backdrop');
            if (forceOpen || drawer.classList.contains('translate-x-full')) {
                drawer.classList.remove('translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                drawer.classList.add('translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        function enviarPedidoWhatsapp() {
            if (carrito.length === 0) {
                alert('Agrega al menos un producto al carrito para enviar el pedido.');
                return;
            }

            let mensaje = `*NUEVO PEDIDO EN LÍNEA*\n\n`;
            let total = 0;
            carrito.forEach(item => {
                const sub = item.precio * item.cantidad;
                total += sub;
                mensaje += `• ${item.nombre} x${item.cantidad} - ${MONEDA} ${sub.toFixed(2)}\n`;
            });
            mensaje += `\n*TOTAL:* ${MONEDA} ${total.toFixed(2)}\n\n`;
            mensaje += `Por favor confirmen disponibilidad y métodos de entrega. ¡Gracias!`;

            const url = `https://api.whatsapp.com/send?phone=${TELEFONO_WHATSAPP}&text=${encodeURIComponent(mensaje)}`;
            window.open(url, '_blank');
        }

        function seleccionarVarianteModal(variante, btnElement) {
            varianteActual = variante;
            document.querySelectorAll('.qv-var-btn').forEach(b => {
                b.classList.remove('bg-[#F97316]', 'text-white', 'border-[#F97316]');
                b.classList.add('bg-slate-50', 'text-slate-700', 'border-slate-200');
            });
            btnElement.classList.remove('bg-slate-50', 'text-slate-700', 'border-slate-200');
            btnElement.classList.add('bg-[#F97316]', 'text-white', 'border-[#F97316]');

            const stockEl = document.getElementById('qv-var-stock');
            if (stockEl) {
                const s = parseInt(variante.stock_actual);
                stockEl.innerText = s > 0 ? `Stock disponible: ${s} unid.` : `Agotado en esta opción`;
                stockEl.className = s > 0 ? 'text-emerald-600 text-[11px] font-bold' : 'text-rose-600 text-[11px] font-bold';
            }
        }

        function abrirQuickView(id) {
            fetch(`${BASE_URL_APP}/tienda/detalle/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const p = data.producto;
                        productoActualModal = p;
                        varianteActual = (p.variantes && p.variantes.length > 0) ? p.variantes[0] : null;

                        const modal = document.getElementById('quickview-modal');
                        const container = document.getElementById('quickview-content');

                        const mainImg = p.imagen ? `${BASE_URL_APP}/${p.imagen}` : 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=500&q=80';
                        
                        let galeriaHTML = `<img id="qv-main-img" src="${mainImg}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=500&q=80';" class="w-full aspect-square object-cover rounded-2xl bg-slate-100 border">`;
                        if (p.imagenes && p.imagenes.length > 0) {
                            galeriaHTML += `<div class="flex gap-2 mt-3 overflow-x-auto pb-1">`;
                            p.imagenes.forEach(img => {
                                const ruta = `${BASE_URL_APP}/${img.ruta_imagen}`;
                                galeriaHTML += `<img src="${ruta}" onclick="document.getElementById('qv-main-img').src='${ruta}'" class="w-14 h-14 object-cover rounded-lg border cursor-pointer hover:opacity-80">`;
                            });
                            galeriaHTML += `</div>`;
                        }

                        // Generar selector de variantes (Talla / Color)
                        let variantesHTML = '';
                        if (p.variantes && p.variantes.length > 0) {
                            variantesHTML = `
                                <div class="space-y-2 pt-2 border-t border-slate-100">
                                    <label class="text-xs font-bold text-slate-800 flex items-center justify-between">
                                        <span>Seleccionar Talla / Presentación:</span>
                                        <span id="qv-var-stock" class="text-emerald-600 text-[11px] font-bold">
                                            Stock: ${p.variantes[0].stock_actual} unid.
                                        </span>
                                    </label>
                                    <div class="flex flex-wrap gap-2">
                                        ${p.variantes.map((v, idx) => {
                                            const label = [v.talla ? 'Talla ' + v.talla : '', v.color || ''].filter(Boolean).join(' - ') || `Opción ${idx+1}`;
                                            const disabled = parseInt(v.stock_actual) <= 0 ? 'opacity-40 pointer-events-none' : '';
                                            return `
                                                <button type="button" 
                                                        onclick='seleccionarVarianteModal(${JSON.stringify(v)}, this)' 
                                                        class="qv-var-btn px-3 py-1.5 rounded-xl border text-xs font-headline font-bold transition-all ${idx === 0 ? 'bg-[#F97316] text-white border-[#F97316]' : 'bg-slate-50 text-slate-700 border-slate-200'} ${disabled}">
                                                    ${label} ${parseInt(v.stock_actual) <= 0 ? '(Agotado)' : ''}
                                                </button>
                                            `;
                                        }).join('')}
                                    </div>
                                </div>
                            `;
                        }

                        container.innerHTML = `
                            <div>${galeriaHTML}</div>
                            <div class="flex flex-col justify-between space-y-4">
                                <div class="space-y-2">
                                    <span class="text-xs font-bold text-[#F97316] uppercase">${p.categoria_nombre || 'General'}</span>
                                    <h3 class="font-headline font-bold text-xl text-slate-900">${p.nombre}</h3>
                                    <div class="font-headline font-extrabold text-2xl text-[#F97316]">${MONEDA} ${parseFloat(p.precio_venta).toFixed(2)}</div>
                                    <p class="text-xs text-slate-600 leading-relaxed">${p.descripcion || 'Sin descripción adicional.'}</p>
                                    ${variantesHTML}
                                </div>
                                <button onclick='agregarAlCarrito(productoActualModal, varianteActual); cerrarQuickView();' class="w-full bg-[#F97316] hover:bg-[#ea580c] text-white font-headline font-bold text-xs py-3 rounded-xl flex items-center justify-center gap-2 shadow-md active:scale-95 transition-all">
                                    <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                    <span>Agregar al Carrito</span>
                                </button>
                            </div>
                        `;

                        modal.classList.remove('hidden');
                    }
                });
        }

        function cerrarQuickView() {
            document.getElementById('quickview-modal').classList.add('hidden');
        }

        function filtrarProductosEnVivo() {
            const query = document.getElementById('live-search').value.toLowerCase();
            const cards = document.querySelectorAll('.producto-card');
            cards.forEach(card => {
                const nombre = card.getAttribute('data-nombre');
                const cat = card.getAttribute('data-categoria');
                if (nombre.includes(query) || cat.includes(query)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Inicializar UI al cargar
        actualizarUI();
    </script>
</body>
</html>
