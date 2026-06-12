<?php
// src/Models/Prestamo.php

class Prestamo
{
    private ?mysqli $db;
    private ?string $table = "prestamos";

    public function __construct(?mysqli $dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Obtiene todos los préstamos
     */
    public function getAll($page = 1)
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $query = "
            SELECT 
                p.id,
                p.usuario_id,
                p.libro_id,
                p.fecha_prestamo,
                p.fecha_devolucion,
                p.estado
            FROM " . $this->table . " p
            ORDER BY p.id DESC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("ii", $limit, $offset);

        $stmt->execute();

        $result = $stmt->get_result();

        $prestamos = [];

        while ($row = $result->fetch_assoc()) {
            $prestamos[] = $row;
        }

        $stmt->close();

        return $prestamos;
    }

    /**
     * Crear préstamo
     */
    public function create(
        int $usuario_id,
        int $libro_id,
        string $fecha_devolucion,
        string $estado
    ) {
        $query = "
            INSERT INTO " . $this->table . "
            (usuario_id, libro_id, fecha_prestamo, fecha_devolucion, estado)
            VALUES (?, ?, NOW(), ?, ?)
        ";

        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "iiss",
            $usuario_id,
            $libro_id,
            $fecha_devolucion,
            $estado,
        );

        $resultado = $stmt->execute();

        $stmt->close();

        return $resultado;
    }

    /**
     * Obtener préstamo por ID
     */
    public function getById(int $id)
    {
        $query = "
            SELECT 
                id,
                usuario_id,
                libro_id,
                fecha_prestamo,
                fecha_devolucion
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

        $prestamo = $result->fetch_assoc();

        $stmt->close();

        return $prestamo;
    }

    /**
     * Eliminar préstamo
     */
    public function delete(int $id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";

        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("id", $id);

        $resultado = $stmt->execute();

        $stmt->close();

        return $resultado;
    }
}