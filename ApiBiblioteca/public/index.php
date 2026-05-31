<?php

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/LibrosController.php';

//Cabeceras globales
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
$database = new Connection();
$dbConn = $database->getConnection();

//Capturar el metodo HTTP y la URL
$method = $_SERVER['REQUEST_METHOD'];
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';

// aca dividimos la URL por slashes (ej: "api/libros/5" -> ['api', 'libros', '5'])
$urlParams = explode('/', $url);

// Asegurarnos de que la ruta comience con "api"
if (empty($urlParams) || $urlParams[0] !== 'api') {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Ruta no encontrada o invalida."]);
    exit();
}

// El siguiente parametro define el recurso (ej: "libros", "usuarios", "login")
$resource = isset($urlParams[1]) ? $urlParams[1] : '';
$id = isset($urlParams[2]) ? (int) $urlParams[2] : null;

//Enrutador basico (Estructura de control)
switch ($resource) {
    case 'login':
        if ($method === 'POST') {
            $authController = new AuthController($dbConn);
            $authController->login();
        } else {
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Metodo no permitido para login."]);
        }
        break;
    case 'register':
        if ($method === 'POST') {
            $authController = new AuthController($dbConn);
            $authController->register();
        } else {
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Metodo no permitido para registro."]);
        }
        break;
    case 'libros':
        $librosController = new LibrosController($dbConn);

        if ($method === 'GET') {
            if ($id === null && !isset($urlParams[2])) {
                $librosController->index();
            } else if (isset($urlParams[2]) && !empty($urlParams[2])) {
                $librosController->search($urlParams[2]);
            } else {
                $librosController->index();
            }
        } else if ($method === 'POST') {
            $librosController->store();
        } else if ($method === 'PUT') {
            if ($id !== null && $id > 0) {
                $librosController->update($id);
            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Se requiere un ID válido."]);
            }
        }
        else if ($method === 'DELETE') {
            if ($id !== null && $id > 0) {
                $librosController->destroy($id);
            } else {
                http_response_code(400);
                echo json_encode([
                    "status" => "error",
                    "message" => "Se requiere un ID numérico válido para eliminar el libro."
                ]);
            }
        } else {
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Método no permitido."]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "El recurso '$resource' no existe."]);
        break;
}






