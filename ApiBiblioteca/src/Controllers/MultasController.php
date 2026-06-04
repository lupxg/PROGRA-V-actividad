<?php
// src/Controllers/MultasController.php

require_once __DIR__ . '/../Models/Multa.php';

class MultasController
{
    private ?mysqli $db;
    private ?Multa $multaModel;

    public function __construct(?mysqli $dbConnection)
    {
        $this->db = $dbConnection;
        $this->multaModel = new Multa($this->db);
    }

    /**
     * GET /api/multas
     */
    public function index()
    {
        $page = isset($_GET['page'])
            ? filter_var($_GET['page'], FILTER_VALIDATE_INT)
            : 1;

        if ($page === false || $page < 1) {
            $page = 1;
        }

        $multas = $this->multaModel->getAll($page);

        http_response_code(200);

        echo json_encode([
            "status" => "success",
            "page" => $page,
            "count" => count($multas),
            "data" => $multas
        ]);
    }

    /**
     * POST /api/multas
     */
    public function store()
    {
        $json = file_get_contents("php://input");

        $input = json_decode($json, true);

        if (
            !$input ||
            !array_key_exists('prestamo_id', $input) ||
            !array_key_exists('monto', $input)
        ) {
            http_response_code(400);

            echo json_encode([
                "status" => "error",
                "message" => "Faltan datos obligatorios (prestamo_id, monto)."
            ]);

            return;
        }

        $prestamo_id = (int) $input['prestamo_id'];
        $monto = (float) $input['monto'];
        $pagada = isset($input['pagada']) ? (int) $input['pagada'] : 0;

        $exito = $this->multaModel->create(
            $prestamo_id,
            $monto,
            $pagada
        );

        if ($exito) {

            http_response_code(201);

            echo json_encode([
                "status" => "success",
                "message" => "Multa creada correctamente."
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "status" => "error",
                "message" => "Error interno al crear la multa."
            ]);
        }
    }

    /**
     * PUT /api/multas/{id}
     */
    public function update(int $id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if ($id === false || $id < 1) {

            http_response_code(400);

            echo json_encode([
                "status" => "error",
                "message" => "El ID de la multa no es válido."
            ]);

            return;
        }

        $multaExistente = $this->multaModel->getById($id);

        if (!$multaExistente) {

            http_response_code(404);

            echo json_encode([
                "status" => "error",
                "message" => "La multa no existe."
            ]);

            return;
        }

        $json = file_get_contents("php://input");

        $input = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {

            http_response_code(400);

            echo json_encode([
                "status" => "error",
                "message" => "JSON inválido."
            ]);

            return;
        }

        $prestamo_id = isset($input['prestamo_id'])
            ? (int) $input['prestamo_id']
            : (int) $multaExistente['prestamo_id'];

        $monto = isset($input['monto'])
            ? (float) $input['monto']
            : (float) $multaExistente['monto'];

        $pagada = isset($input['pagada'])
            ? (int) $input['pagada']
            : (int) $multaExistente['pagada'];

        $exito = $this->multaModel->update(
            $id,
            $prestamo_id,
            $monto,
            $pagada
        );

        if ($exito) {

            http_response_code(200);

            echo json_encode([
                "status" => "success",
                "message" => "Multa actualizada correctamente."
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "status" => "error",
                "message" => "Error interno al actualizar la multa."
            ]);
        }
    }

    /**
     * DELETE /api/multas/{id}
     */
    public function destroy(int $id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if ($id === false || $id < 1) {

            http_response_code(400);

            echo json_encode([
                "status" => "error",
                "message" => "El ID de la multa no es válido."
            ]);

            return;
        }

        $multa = $this->multaModel->getById($id);

        if (!$multa) {

            http_response_code(404);

            echo json_encode([
                "status" => "error",
                "message" => "La multa solicitada no existe."
            ]);

            return;
        }

        $exito = $this->multaModel->delete($id);

        if ($exito) {

            http_response_code(200);

            echo json_encode([
                "status" => "success",
                "message" => "Multa eliminada correctamente."
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "status" => "error",
                "message" => "Error interno al eliminar la multa."
            ]);
        }
    }
}