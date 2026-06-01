<?php
// src/Models/Multa.php

class Multa
{
    private ?mysqli $db;
    private ?string $table = "multas";

    public function __construct(?mysqli $dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Obtener multas paginadas
     */
    public function getAll($page = 1)
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $query = "
            SELECT 
                id,
                prestamo_id,
                monto,
                pagada
            FROM " . $this->table . "
            ORDER BY id DESC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("ii", $limit, $offset);

        $stmt->execute();

        $result = $stmt->get_result();

        $multas = [];

        while ($row = $result->fetch_assoc()) {
            $multas[] = $row;
        }

        $stmt->close();

        return $multas;
    }

    /**
     * Obtener multa por ID
     */
    public function getById(int $id)
    {
        $query = "
            SELECT 
                id,
                prestamo_id,
                monto,
                pagada
            FROM " . $this->table . "
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("i", $id);

        $stmt->execute();

        $result = $stmt->get_result();

        $multa = $result->fetch_assoc();

        $stmt->close();

        return $multa;
    }

    /**
     * Crear multa
     */
    public function create(
        int $prestamo_id,
        float $monto,
        int $pagada
    ) {
        $query = "
            INSERT INTO " . $this->table . "
            (prestamo_id, monto, pagada)
            VALUES (?, ?, ?)
        ";

        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "idi",
            $prestamo_id,
            $monto,
            $pagada
        );

        $resultado = $stmt->execute();

        $stmt->close();

        return $resultado;
    }

    /**
     * Actualizar multa
     */
    public function update(
        int $id,
        int $prestamo_id,
        float $monto,
        int $pagada
    ) {
        $query = "
            UPDATE " . $this->table . "
            SET 
                prestamo_id = ?,
                monto = ?,
                pagada = ?
            WHERE id = ?
        ";

        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "idii",
            $prestamo_id,
            $monto,
            $pagada,
            $id
        );

        $resultado = $stmt->execute();

        $stmt->close();

        return $resultado;
    }

    /**
     * Eliminar multa
     */
    public function delete(int $id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";

        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $id);

        $resultado = $stmt->execute();

        $stmt->close();

        return $resultado;
    }
}