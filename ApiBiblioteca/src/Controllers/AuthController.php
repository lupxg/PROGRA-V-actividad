<?php

require_once __DIR__ . '/../Models/Usuario.php';

class AuthController
{
    private ?mysqli $db;
    private ?Usuario $usuarioModel;

    public function __construct(?mysqli $dbConnection)
    {
        $this->db = $dbConnection;
        $this->usuarioModel = new Usuario($this->db);
    }

    public function login()
    {
        $json = file_get_contents("php://input");
        $input = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['correo']) || !isset($input['password'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Petición invalida. Faltan datos obligatorios (correo y password)."
            ]);
            return;
        }

        $correo = trim($input['correo']);
        $password = $input['password'];

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "El formato del correo electronico no es valido."
            ]);
            return;
        }

        $usuario = $this->usuarioModel->getByEmail($correo);

        if ($usuario && password_verify($password, $usuario['password'])) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Inicio de sesión exitoso.",
                "user" => [
                    "id" => $usuario['id'],
                    "nombre" => $usuario['nombre'],
                    "correo" => $usuario['correo'],
                    "rol" => $usuario['rol']
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "status" => "error",
                "message" => "Correo electrónico o contraseña incorrectos."
            ]);
        }
    }
    public function register()
    {
        $json = file_get_contents("php://input");
        $input = json_decode($json, true);

       
        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['nombre']) || !isset($input['correo']) || !isset($input['password']) || !isset($input['rol'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Faltan datos obligatorios (nombre, correo, password y rol)."
            ]);
            return;
        }

       
        $nombre = trim(htmlspecialchars($input['nombre'])); 
        $correo = trim($input['correo']);
        $password_plana = $input['password'];
        $rol = trim($input['rol']);

        
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "El formato del correo electronico no es valido."
            ]);
            return;
        }

       
        $roles_permitidos = ['admin', 'bibliotecario', 'lector'];
        if (!in_array($rol, $roles_permitidos)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "El rol especificado no es valido."
            ]);
            return;
        }

        if (strlen($password_plana) < 6) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "La contraseña debe tener al menos 6 caracteres."
            ]);
            return;
        }
        
        if ($this->usuarioModel->emailExists($correo)) {
            http_response_code(409); 
            echo json_encode([
                "status" => "error",
                "message" => "El correo electrónico ya se encuentra registrado."
            ]);
            return;
        }

        $password_encriptada = password_hash($password_plana, PASSWORD_BCRYPT);

        $registro_exitoso = $this->usuarioModel->create($nombre, $correo, $password_encriptada, $rol);

        if ($registro_exitoso) {
            http_response_code(201); 
            echo json_encode([
                "status" => "success",
                "message" => "Usuario registrado exitosamente."
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Ocurrio un error interno al registrar el usuario."
            ]);
        }
    }

}