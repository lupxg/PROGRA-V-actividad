<?php
// router.php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Si la ruta corresponde a un archivo real dentro de public/ (como un .css, .js o imagen), que lo sirva directamente
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

// Para cualquier otra ruta (como /api/login), redirigimos el tráfico al index.php
include_index();

function include_index() {
    // Definimos la variable url que espera nuestro public/index.php original
    $_GET['url'] = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    require_once __DIR__ . '/public/index.php';
}