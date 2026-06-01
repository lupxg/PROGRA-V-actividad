<?php
// src/Models/Usuario.php

class Usuario
{
    private ?mysqli $db;
    private string $table = "usuarios";

    public function __construct(?mysqli $dbConnection)
    {
        $this->db = $dbConnection;
    }

    // Buscar un usuario por su correo
    public function getByEmail(string $correo)
    {
        $query = "SELECT id, nombre, correo, password, rol FROM " . $this->table . " WHERE correo = ? LIMIT 1";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("s", $correo);
        $stmt->execute();

        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();

        $stmt->close();

        return $usuario;
    }
    public function emailExists(string $correo)
    {
        $query = "SELECT id FROM " . $this->table . " WHERE correo = ? LIMIT 1";
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            return true; 
        }

        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result(); 

        $exists = $stmt->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    public function create(string $nombre, string $correo, string $password_encriptada, string $rol)
    {
        $query = "INSERT INTO " . $this->table . " (nombre, correo, password, rol) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            return false;
        }

        
        $stmt->bind_param("ssss", $nombre, $correo, $password_encriptada, $rol);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

    
    /**
     * Obtener usuario por ID
     */
    public function getById(int $id)
    {
        $query = "
            SELECT 
                id,
                nombre,
                correo,
                rol
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

        $usuario = $result->fetch_assoc();

        $stmt->close();

        return $usuario;
    }

    /**
     * Actualizar usuario
     */
    public function update(
        int $id,
        string $nombre,
        string $correo,
        string $password,
        string $rol
    ) {

        $query = "
            UPDATE " . $this->table . "
            SET
                nombre = ?,
                correo = ?,
                password = ?,
                rol = ?
            WHERE id = ?
        ";

        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "ssssi",
            $nombre,
            $correo,
            $password,
            $rol,
            $id
        );

        $resultado = $stmt->execute();

        $stmt->close();

        return $resultado;
    }

    /**
     * Eliminar usuario
     */
    public function delete(int $id)
    {
        $query = "
            DELETE FROM " . $this->table . "
            WHERE id = ?
        ";

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