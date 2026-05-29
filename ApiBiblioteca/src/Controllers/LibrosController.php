<?php
// src/Controllers/LibrosController.php

require_once __DIR__ . '/../Models/Libro.php';

class LibrosController
{
    private ?mysqli $db;
    private ?Libro $libroModel;

    public function __construct(?mysqli $dbConnection)
    {
        $this->db = $dbConnection;
        $this->libroModel = new Libro($this->db);
    }

    /**
     * Maneja la petición de listar todos los libros con paginación
     */
    public function index()
    {

        $page = isset($_GET['page']) ? filter_var($_GET['page'], FILTER_VALIDATE_INT) : 1;

        if ($page === false || $page < 1) {
            $page = 1;
        }
        $libros = $this->libroModel->getAll($page);

        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "page" => $page,
            "count" => count($libros),
            "data" => $libros
        ]);
    }
    public function search(?string $titulo)
    {
        $termino = trim(urldecode($titulo));

        if (empty($termino)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "El término de búsqueda no puede estar vacío."
            ]);
            return;
        }
        $libros = $this->libroModel->searchByTitulo($termino);

        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "query" => $termino,
            "count" => count($libros),
            "data" => $libros
        ]);
    }
    public function store()
    {
        $json = file_get_contents("php://input");
        $input = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['titulo']) || !isset($input['autor']) || !isset($input['categoria']) || !isset($input['stock'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Faltan datos obligatorios (titulo, autor, categoria y stock)."
            ]);
            return;
        }

        $titulo = trim(htmlspecialchars($input['titulo']));
        $autor = trim(htmlspecialchars($input['autor']));
        $categoria = trim(htmlspecialchars($input['categoria']));
        $stock = (int) $input['stock'];
        $disponible = isset($input['disponible']) ? (int) $input['disponible'] : 1;

        $nombreImagen = null;
        if (isset($input['imagen_base64']) && !empty($input['imagen_base64'])) {

            $nombreImagen = $this->procesarImagenBase64($input['imagen_base64']);

            if ($nombreImagen === null) {
                http_response_code(400);
                echo json_encode([
                    "status" => "error",
                    "message" => "La imagen enviada no es válida o el formato no está permitido (solo jpg, jpeg, png, webp)."
                ]);
                return;
            }
        }

        $exito = $this->libroModel->create($titulo, $autor, $categoria, $stock, $disponible, $nombreImagen);

        if ($exito) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Libro creado exitosamente con su portada."
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Error interno del servidor al intentar registrar el libro."
            ]);
        }
    }

    public function update($id)
    {
        // verificacion del id
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "El ID del libro no es válido."]);
            return;
        }
        // trae y verificamos si existe el libro con ese id
        $libroExistente = $this->libroModel->getById($id);
        if (!$libroExistente) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "El libro solicitado no existe."]);
            return;
        }

        $json = file_get_contents("php://input");
        $input = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "El cuerpo de la petición no es un JSON válido."]);
            return;
        }

        $titulo = isset($input['titulo']) ? trim(htmlspecialchars($input['titulo'])) : $libroExistente['titulo'];
        $autor = isset($input['autor']) ? trim(htmlspecialchars($input['autor'])) : $libroExistente['autor'];
        $categoria = isset($input['categoria']) ? trim(htmlspecialchars($input['categoria'])) : $libroExistente['categoria'];
        $stock = isset($input['stock']) ? (int) $input['stock'] : (int) $libroExistente['stock'];
        $disponible = isset($input['disponible']) ? (int) $input['disponible'] : (int) $libroExistente['disponible'];

        $nombreImagen = $libroExistente['imagen_url'];

        if (isset($input['imagen_base64']) && !empty($input['imagen_base64'])) {
            $nuevaImagen = $this->procesarImagenBase64($input['imagen_base64']);

            if ($nuevaImagen === null) {
                http_response_code(400);
                echo json_encode([
                    "status" => "error",
                    "message" => "La nueva imagen no es válida o el formato no está permitido."
                ]);
                return;
            }

            if (!empty($libroExistente['imagen_url'])) {
                $rutaImagenVieja = __DIR__ . '/../../public/uploads/' . $libroExistente['imagen_url'];
                if (file_exists($rutaImagenVieja)) {
                    unlink($rutaImagenVieja); // Borra fisicamente el archivo viejo
                }
            }

            $nombreImagen = $nuevaImagen;
        }

        $exito = $this->libroModel->update($id, $titulo, $autor, $categoria, $stock, $disponible, $nombreImagen);

        if ($exito) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Libro actualizado correctamente."
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Error interno al intentar actualizar el libro."
            ]);
        }
    }

    public function destroy(int $id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "El ID del libro no es válido."]);
            return;
        }

        $libro = $this->libroModel->getById($id);
        if (!$libro) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "El libro que intenta eliminar no existe."]);
            return;
        }

        if (!empty($libro['imagen_url'])) {
            $rutaImagen = __DIR__ . '/../../public/uploads/' . $libro['imagen_url'];
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen); // Borra fisicamente la portada
            }
        }

        $exito = $this->libroModel->delete($id);

        if ($exito) {
            http_response_code(200); 
            echo json_encode([
                "status" => "success",
                "message" => "Libro y su archivo de portada eliminados correctamente."
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Error interno al intentar eliminar el libro de la base de datos."
            ]);
        }
    }

    private function procesarImagenBase64($base64String)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {

            $extension = strtolower($type[1]); // png, jpeg, jpg, gif

            $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($extension, $extensiones_permitidas)) {
                return null;
            }


            $base64Data = substr($base64String, strpos($base64String, ',') + 1);


            $imagenDecodificada = base64_decode($base64Data);
            if ($imagenDecodificada === false) {
                return null;
            }

            $nombreArchivo = bin2hex(random_bytes(8)) . '.' . $extension;

            $rutaDestino = __DIR__ . '/../../public/uploads/' . $nombreArchivo;

            if (file_put_contents($rutaDestino, $imagenDecodificada)) {
                return $nombreArchivo;
            }
        }

        return null;
    }
}