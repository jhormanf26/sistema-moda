<?php
// public/index.php

// Helper global de formato para Pesos Colombianos (COP)
if (!function_exists('formatearCOP')) {
    function formatearCOP($monto) {
        if ($monto === null || $monto === '') return '0';
        return number_format((float)$monto, 0, ',', '.');
    }
}

// 1. CONFIGURACIÓN DE URL DINÁMICA
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$folder = ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim($scriptDir, '/');
define('BASE_URL', $protocol . '://' . $host . $folder);

// 2. CARGA DE LIBRERÍAS DE COMPOSER (CRÍTICO: SOLUCIONA ERROR 'CLASS NOT FOUND')
require_once '../vendor/autoload.php';

// 3. REQUERIR BASE DE DATOS Y CORE
require_once '../app/core/Database.php';
require_once '../app/core/License.php';

// 4. AUTOLOAD (Carga automática de clases propias)
spl_autoload_register(function ($nombre_clase) {
    if (file_exists('../app/controllers/' . $nombre_clase . '.php')) {
        require_once '../app/controllers/' . $nombre_clase . '.php';
    } elseif (file_exists('../app/models/' . $nombre_clase . '.php')) {
        require_once '../app/models/' . $nombre_clase . '.php';
    }
});

// Interceptar con validación de Licencia Offline
License::checkEarly();

// 5. SISTEMA DE RUTAS
$url = isset($_GET['url']) ? $_GET['url'] : 'Tienda/index';
$url = rtrim($url, '/');
$url = explode('/', $url);

// Controlador
$controladorNombre = (!empty($url[0])) ? ucwords($url[0]) . 'Controller' : 'TiendaController';
$metodo = (!empty($url[1])) ? $url[1] : 'index';
$parametros = isset($url[2]) ? array_slice($url, 2) : [];

// 6. EJECUTAR CONTROLADOR
if (file_exists('../app/controllers/' . $controladorNombre . '.php')) {
    $controlador = new $controladorNombre();
    
    if (method_exists($controlador, $metodo)) {
        call_user_func_array([$controlador, $metodo], $parametros);
    } else {
        echo "Error 404: El método no existe.";
    }
} else {
    header('Location: ' . BASE_URL . '/tienda/index');
}