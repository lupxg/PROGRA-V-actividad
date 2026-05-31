CREATE DATABASE IF NOT EXISTS biblioteca;
USE biblioteca;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'bibliotecario', 'lector') NOT NULL
);

CREATE TABLE libros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    autor VARCHAR(100) NOT NULL,
    categoria VARCHAR(100),
    stock INT NOT NULL DEFAULT 0,
    disponible INT NOT NULL DEFAULT 0
);

CREATE TABLE prestamos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    libro_id INT,
    fecha_prestamo DATE NOT NULL,
    fecha_devolucion DATE,
    estado ENUM('prestado', 'devuelto') DEFAULT 'prestado',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (libro_id) REFERENCES libros(id) ON DELETE CASCADE
);

CREATE TABLE multas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prestamo_id INT,
    monto DECIMAL(10,2) NOT NULL,
    pagada BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (prestamo_id) REFERENCES prestamos(id) ON DELETE CASCADE
);


-- INSERT INTO usuarios (nombre, correo, password, rol) VALUES ( 'Guillermo Chávez', 'guillermo@correo.com', '$2y$10$vGZlyB/zQJ0bHwJ2k3mFauXvLox0lW2R3tZlV1/I.oZExKRE85nba', 'admin');

SELECT * FROM usuarios;
SELECT * FROM Libros;
ALTER TABLE libros ADD COLUMN imagen_url VARCHAR(255) NULL AFTER disponible;
