<?php

class Libro
{
    private ?mysqli $db;
    private ?string $table = "libros";

    public function __construct(?mysqli $dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Obtiene los libros paginados de 10 en 10.
     * @param int $page Número de página actual
     * @return array Lista de libros encontrados
     */
    // src/Models/Libro.php

    public function getAll($page = 1)
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;


        $query = "SELECT id, titulo, autor, categoria, stock, disponible, imagen_url FROM " . $this->table . " LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();

        $result = $stmt->get_result();
        $libros = [];

        while ($row = $result->fetch_assoc()) {

            if (!empty($row['imagen_url'])) {
                $row['imagen_url'] = "http://localhost:3000/uploads/" . $row['imagen_url'];
            }
            $libros[] = $row;
        }

        $stmt->close();
        return $libros;
    }

    public function searchByTitulo(?string $termino)
    {
        $query = "SELECT id, titulo, autor, categoria, stock, disponible, imagen_url FROM " . $this->table . " WHERE titulo LIKE ?";

        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            return [];
        }

        $busquedaAproximada = "%" . $termino . "%";


        $stmt->bind_param("s", $busquedaAproximada);
        $stmt->execute();

        $result = $stmt->get_result();
        $libros = [];

        while ($row = $result->fetch_assoc()) {

            if (!empty($row['imagen_url'])) {
                $row['imagen_url'] = "http://localhost:3000/uploads/" . $row['imagen_url'];
            }
            $libros[] = $row;
        }

        $stmt->close();
        return $libros;
    }

    public function create(string $titulo, string $autor, string $categoria, int $stock, int $disponible, string $imagen_url)
    {
        $query = "INSERT INTO " . $this->table . " (titulo, autor, categoria, stock, disponible, imagen_url) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ssiiis", $titulo, $autor, $categoria, $stock, $disponible, $imagen_url);

        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

    public function getById(int $id)
    {
        $query = "SELECT id, titulo, autor, categoria, stock, disponible, imagen_url FROM " . $this->table . " WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($query);

        if (!$stmt)
            return null;

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $libro = $result->fetch_assoc();
        $stmt->close();

        return $libro;
    }


    public function update(int $id, $titulo, $autor, $categoria, $stock, $disponible, $imagen_url)
    {
        $query = "UPDATE " . $this->table . " SET titulo = ?, autor = ?, categoria = ?, stock = ?, disponible = ?, imagen_url = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ssiiisi", $titulo, $autor, $categoria, $stock, $disponible, $imagen_url, $id);

        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

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
