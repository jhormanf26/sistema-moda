<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Apertura de Caja - Sistema Moda</title>
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
    
    <div class="container-fluid h-100 d-flex align-items-center justify-content-center p-4">
        
        <div class="card card-custom border-0 shadow-sm p-4 text-center" style="width: 100%; max-width: 450px;">
            <div class="mb-4">
                <div class="d-inline-flex justify-content-center align-items-center rounded-circle shadow-sm" style="width: 80px; height: 80px; background-color: rgba(201,132,122,0.15); color: var(--primary);">
                    <i class="bi bi-shop-window display-4"></i>
                </div>
            </div>
            
            <h3 class="fw-bold mb-2 text-dark">Apertura de Turno</h3>
            <p class="text-muted mb-4 small">
                Hola, <strong><?= $_SESSION['user_nombre'] ?></strong>.<br>
                Ingresa el dinero base en caja para comenzar.
            </p>
            
            <form action="<?= BASE_URL ?>/caja/abrir" method="POST">
                <div class="form-floating mb-4">
                    <input type="number" step="0.01" name="monto" class="form-control form-control-lg fw-bold text-center fs-3 shadow-sm border-0 bg-light" id="montoInput" placeholder="0.00" style="color: var(--primary);" required autofocus>
                    <label for="montoInput" class="text-muted fw-bold tracking-wide">Monto Inicial (<?= htmlspecialchars($monedaEmpresa ?? 'S/') ?>)</label>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm" style="letter-spacing: 1px;">
                        <i class="bi bi-unlock-fill me-2"></i> ABRIR CAJA
                    </button>
                </div>
            </form>
            
            <div class="mt-4 pt-3 border-top">
                <small class="text-muted">¿No eres tú?</small><br>
                <a href="<?= BASE_URL ?>/auth/logout" class="text-decoration-none text-danger fw-bold">
                    <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
                </a>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
