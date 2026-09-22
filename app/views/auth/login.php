<?php
require_once dirname(__DIR__, 2) . '/core/Database.php';
$db = new Database();
$conn = $db->getConnection();
$stmtEmpresa = $conn ? $conn->query("SELECT * FROM empresa WHERE id = 1") : false;
$empresaGlobal = $stmtEmpresa ? $stmtEmpresa->fetch(PDO::FETCH_ASSOC) : null;
$logoEmpresa = $empresaGlobal['logo'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Sistema Moda Boutique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <style>
        body {
            background-color: var(--bg-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-wrapper {
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            border-radius: 20px;
            overflow: hidden;
            background: #fff;
            max-width: 1000px;
            width: 100%;
            margin: auto;
            display: flex;
            min-height: 600px;
        }
        .login-image {
            flex: 1.2;
            background: url('<?= BASE_URL ?>/img/login_bg.jpg') center/cover no-repeat;
            position: relative;
        }
        .login-image::after {
            content: '';
            position: absolute;
            top:0; left:0; width:100%; height:100%;
            background: linear-gradient(135deg, rgba(201,132,122,0.4) 0%, rgba(43,36,49,0.7) 100%);
        }
        .login-image-content {
            position: absolute;
            bottom: 40px;
            left: 40px;
            z-index: 2;
            color: white;
            padding-right: 20px;
        }
        .login-form-container {
            flex: 1;
            padding: 3rem 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--bg-main);
        }
        .login-input {
            background-color: rgba(255,255,255,0.7);
            border: 1px solid var(--border-color);
            padding: 0.8rem 1rem;
            border-radius: 10px;
            font-size: 1rem;
        }
        .login-input:focus {
            background-color: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(201,132,122,0.25);
        }
        .brand-logo-text {
            font-family: 'Playfair Display', serif;
            font-weight: 800;
            color: var(--bg-dark);
            font-size: 2rem;
            letter-spacing: -1px;
            margin-bottom: 0.5rem;
        }
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                margin: 20px;
                border-radius: 15px;
            }
            .login-image {
                display: none; /* Ocultar imagen en móvil para dar prioridad al form */
            }
            .login-form-container {
                padding: 2.5rem 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="login-wrapper">
        <!-- Lado Izquierdo: Imagen -->
        <div class="login-image d-none d-md-block">
            <div class="login-image-content">
                <h2 class="fw-bold mb-2 font-monospace" style="font-size: 2.5rem; color: #f8dbb2; text-shadow: 2px 2px 6px rgba(0,0,0,0.8);"><i class="bi bi-stars text-warning" style="text-shadow: none;"></i> Beauty & Accessories</h2>
                <p class="fs-5 opacity-85 mb-0" style="max-width:300px; color: #ffffff; text-shadow: 1px 1px 4px rgba(0,0,0,0.8);">Controla tus ventas, inventario y sucursales desde un solo lugar con la elegancia que tu marca merece.</p>
            </div>
        </div>

        <!-- Lado Derecho: Formulario -->
        <div class="login-form-container">
            <div class="text-center mb-5">
                <?php if ($logoEmpresa): ?>
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($logoEmpresa) ?>" alt="Logo" class="mb-3 shadow-sm" style="max-height: 85px; border-radius: 12px; object-fit: contain;">
                <?php else: ?>
                    <div class="d-inline-block bg-primary text-white rounded-circle p-3 mb-3 shadow-sm">
                        <i class="bi bi-shop fs-1"></i>
                    </div>
                <?php endif; ?>
                <h1 class="brand-logo-text">Sistema</h1>
                <p class="text-muted fw-bold text-uppercase" style="letter-spacing: 2px; font-size: 0.75rem;">Control de Acceso</p>
            </div>

            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger border-0 shadow-sm text-center py-2 mb-4 rounded-3 d-flex align-items-center justify-content-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <span>Correo o contraseña incorrectos</span>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/auth/acceder" method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 border-light-subtle rounded-start-3 text-muted">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" name="correo" class="form-control login-input border-start-0 ps-0" 
                               placeholder="admin@tienda.com" required autofocus>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-0">Contraseña</label>
                        <a href="#" class="text-decoration-none small text-primary fw-bold" style="font-size: 0.75rem;">¿Olvidaste tu clave?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 border-light-subtle rounded-start-3 text-muted">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" name="password" class="form-control login-input border-start-0 ps-0" 
                               placeholder="••••••••" required>
                    </div>
                </div>

                <div class="d-grid mt-5">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold rounded-pill shadow-sm py-3" style="letter-spacing: 1px;">
                        INGRESAR AL SISTEMA <i class="bi bi-arrow-right-short fs-5 ms-1"></i>
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>