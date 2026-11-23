<?php
/**
 * Test de verificación de paleta de colores para la tienda pública.
 * Valida la sustitución de la paleta naranja por la paleta lila / morado elegante.
 */

$viewPath = __DIR__ . '/../app/views/tienda/index.php';

if (!file_exists($viewPath)) {
    echo "FAIL: El archivo de la vista no existe.\n";
    exit(1);
}

$content = file_get_contents($viewPath);

// 1. Validar que no existan remanentes del naranja
$orangePatterns = [
    '#F97316',
    '#ea580c',
    'bg-orange-50',
    'border-orange-200',
    'border-orange-300'
];

foreach ($orangePatterns as $pattern) {
    if (strpos($content, $pattern) !== false) {
        echo "FAIL: Se encontró el patrón naranja legado '$pattern' en la vista.\n";
        exit(1);
    }
}

// 2. Validar que existan los nuevos colores lila y morado
$requiredLilacPurple = [
    '#8B5CF6',
    '#7C3AED',
    'bg-purple-50',
    'border-purple-200/80',
    'hover:border-purple-300'
];

foreach ($requiredLilacPurple as $required) {
    if (strpos($content, $required) === false) {
        echo "FAIL: No se encontró el nuevo elemento de estilo '$required'.\n";
        exit(1);
    }
}

echo "SUCCESS: Todos los colores naranjas fueron reemplazados exitosamente por la paleta lila / morado (#8B5CF6 / #7C3AED / purple-50).\n";
exit(0);
