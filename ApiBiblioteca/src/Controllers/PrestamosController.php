<?php
// src/Controllers/PrestamosController.php

require_once __DIR__ . '/../Models/Prestamo.php';

class PrestamosController
{
    private ?mysqli $db;
    private ?Prestamo $prestamoModel;

    public function __construct(?mysqli $dbConnection)
    {
        $this->db = $dbConnection;
        $this->prestamoModel = new Prestamo($this->db);
    }

    /**
     * GET /api/prestamos
     * Lista todos los préstamos
     */
    public function index()
    {
        $page = isset($_GET['page']) ? filter_var($_GET['page'], FILTER_VALIDATE_INT) : 1;

        if ($page === false || $page < 1) {
            $page = 1;
        }

        $prestamos = $this->prestamoModel->getAll($page);

        http_response_code(200);

        echo json_encode([
            "status" => "success",
            "page" => $page,
            "count" => count($prestamos),
            "data" => $prestamos
        ]);
    }

    /**
     * POST /api/prestamos
     * Crea un nuevo préstamo
     */
    public function store()
    {
        $json = file_get_contents("php://input");
        $input = json_decode($json, true);

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !isset($input['usuario_id']) ||
            !isset($input['libro_id']) ||
            !isset($input['fecha_devolucion']) ||
            !isset($input['estado'])
        ) {
            http_response_code(400);

            echo json_encode([
                "status" => "error",
                "message" => "Faltan datos obligatorios (usuario_id, libro_id, fecha_devolucion)."
            ]);

            return;
        }

        $usuario_id = (int) $input['usuario_id'];
        $libro_id = (int) $input['libro_id'];
        $fecha_devolucion = trim($input['fecha_devolucion']);
        $estado = $input['estado'];

        if ($usuario_id < 1 || $libro_id < 1) {
            http_response_code(400);

            echo json_encode([
                "status" => "error",
                "message" => "usuario_id y libro_id deben ser válidos."
            ]);

            return;
        }

        $exito = $this->prestamoModel->create(
            $usuario_id,
            $libro_id,
            $fecha_devolucion,
            $estado
        );

        if ($exito) {
            http_response_code(201);

            echo json_encode([
                "status" => "success",
                "message" => "Préstamo creado correctamente."
            ]);
        } else {
            http_response_code(500);

            echo json_encode([
                "status" => "error",
                "message" => "Error interno al crear el préstamo."
            ]);
        }
    }

    /**
     * DELETE /api/prestamos/{id}
     * Elimina un préstamo
     */
    public function destroy(int $id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if ($id === false || $id < 1) {
            http_response_code(400);

            echo json_encode([
                "status" => "error",
                "message" => "El ID del préstamo no es válido."
            ]);

            return;
        }

        $prestamo = $this->prestamoModel->getById($id);

        if (!$prestamo) {
            http_response_code(404);

            echo json_encode([
                "status" => "error",
                "message" => "El préstamo solicitado no existe."
            ]);

            return;
        }

        $exito = $this->prestamoModel->delete($id);

        if ($exito) {
            http_response_code(200);

            echo json_encode([
                "status" => "success",
                "message" => "Préstamo eliminado correctamente."
            ]);
        } else {
            http_response_code(500);

            echo json_encode([
                "status" => "error",
                "message" => "Error interno al eliminar el préstamo."
            ]);
        }
    }
}