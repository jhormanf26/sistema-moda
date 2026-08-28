<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Configuración - Sistema Moda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* FIX DE LAYOUT CRÍTICO: Asegura que el contenido se mueva a la derecha del sidebar fijo */
        @media (min-width: 992px) {
            .offcanvas-lg {
                position: fixed !important;
                top: 0;
            }

            .main-content-area {
                margin-left: 260px;
                /* Ancho del sidebar */
                width: calc(100% - 260px);
            }
        }

        @media (max-width: 767px) {
            .offcanvas-open {
                overflow: hidden !important;
            }
        }
    </style>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark d-lg-none shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold">Sistema Moda</span>
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu"
                aria-controls="sidebarMenu">
                <i class="bi bi-list"></i> Menú
            </button>
        </div>
    </nav>

    <?php include '../app/views/layouts/sidebar.php'; ?>

    <div class="main-content-area" style="height: 100vh; overflow-y: auto;">
        <div class="container-fluid p-4">

            <h2 class="fw-bold mb-4"><i class="bi bi-gear-fill"></i> Configuración del Negocio</h2>

            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle-fill me-2"></i> Datos actualizados correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0" style="max-width: 800px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="m-0 text-primary fw-bold">Datos de la Empresa (Para el Ticket)</h5>
                </div>
                <div class="card-body p-4">

                    <form action="<?= BASE_URL ?>/config/guardar" method="POST" enctype="multipart/form-data">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombre de la Tienda</label>
                                <input type="text" name="nombre" class="form-control"
                                    value="<?= htmlspecialchars($datos['nombre']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">RUC / NIT</label>
                                <input type="text" name="ruc" class="form-control"
                                    value="<?= htmlspecialchars($datos['ruc']) ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Logo de la Empresa</label>
                                <input type="file" name="logo" class="form-control"
                                    accept="image/jpeg, image/png, image/jpg, image/webp">
                                <?php if (!empty($datos['logo'])): ?>
                                    <div class="mt-2">
                                        <span class="text-muted small">Logo actual:</span><br>
                                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($datos['logo']) ?>" alt="Logo actual"
                                            style="max-height: 50px; background: #fff; border-radius: 4px; border: 1px solid #ddd; padding: 4px;">
                                    </div>
                                <?php endif; ?>
                                <div class="form-text">Formatos válidos: JPG, PNG, WEBP.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Símbolo de Moneda</label>
                                <input type="text" name="moneda" class="form-control"
                                    value="<?= htmlspecialchars($datos['moneda'] ?? 'S/') ?>" required
                                    placeholder="Ej: S/ o $">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dirección Fiscal</label>
                            <input type="text" name="direccion" class="form-control"
                                value="<?= htmlspecialchars($datos['direccion']) ?>">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control"
                                    value="<?= htmlspecialchars($datos['telefono']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="email" class="form-control"
                                    value="<?= htmlspecialchars($datos['email']) ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Mensaje al pie del Ticket</label>
                            <textarea name="mensaje" class="form-control"
                                rows="3"><?= htmlspecialchars($datos['mensaje_ticket']) ?></textarea>
                            <div class="form-text">Ej: "Gracias por su compra", "No se aceptan devoluciones", "Visite
                                nuestra web".</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm px-5">
                                <i class="bi bi-save me-2"></i> Guardar Cambios
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
