<?php
define('DB_HOST', 'localhost'); 
define('DB_USER', 'root');      
define('DB_PASS', 'root');          
define('DB_NAME', 'biblioteca');

class Connection {
    private? mysqli $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($this->conn->connect_error) {
                throw new Exception("Error de conexion: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8mb4");
            return $this->conn;

        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "No se pudo conectar a la base de datos.",
                "error" => $e->getMessage()
            ]);
            exit; 
        }
    }
}