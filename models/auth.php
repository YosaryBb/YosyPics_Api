<?php

namespace models;

require_once __DIR__ . "../../utils/utils.php";
require_once __DIR__ . "/model.php";

use utils\Utils;
use models\Model;

class auth extends Model
{
    protected $table_user = 'usuario';
    protected $table_token = 'token_acceso';
    protected $table_profile = 'perfiles';

    public function login($input)
    {
        $email = mysqli_real_escape_string($this->connection, $input['correo']);
        $password = mysqli_real_escape_string($this->connection, $input['password']);
        $password = Utils::hash($password);

        $query = "SELECT * FROM $this->table_user WHERE correo = '$email' AND password = '$password'";

        $result = mysqli_query($this->connection, $query);

        if (mysqli_num_rows($result) > 0) {
            return [
                'status' => true,
                'message' => 'A iniciado sesión con éxito.',
                'token' => $this->guardarTokenLogin(mysqli_fetch_assoc($result)['id']),
            ];
        }

        return [
            'status' => false,
            'message' => 'El correo electrónico o la contraseña son incorrectos',
        ];
    }

    public function guardarTokenLogin($id = null)
    {
        if ($id) {
            $token = Utils::createToken();
            $date = Utils::timestamps();
            $query = "INSERT INTO $this->table_token (id_usuario, token, nombre, fecha ) VALUES ('$id', '$token', 'Login token', '$date')";
            mysqli_query($this->connection, $query);
            return $token;
        }

        return null;
    }

    public function register($input)
    {
        $email = mysqli_real_escape_string($this->connection, $input['correo']);
        $password = mysqli_real_escape_string($this->connection, $input['password']);
        $password = Utils::hash($password);
        $name = mysqli_real_escape_string($this->connection, $input['nombre']);
        $surname = mysqli_real_escape_string($this->connection, $input['apellido']);
        $verifyToken = Utils::createToken(95);

        $query = "INSERT INTO $this->table_user (correo, password, token_verificacion) VALUES ('$email', '$password', '$verifyToken')";

        $result = mysqli_query($this->connection, $query);

        if ($result) {
            $user_id = mysqli_insert_id($this->connection);

            $query = "INSERT INTO $this->table_profile (id_usuario, nombre, apellido, imagen ) VALUES ('$user_id', '$name', '$surname', null)";

            $result = mysqli_query($this->connection, $query);

            if ($result) {
                self::sendMail($email, $verifyToken);

                return [
                    'status' => true,
                    'message' => 'Registro realizado con éxito, ahora verifica tu correo electrónico para activar tu cuenta.',
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Error al registrar al usuario, por favor intente nuevamente.',
                ];
            }
        } else {
            return [
                'status' => false,
                'message' => 'Error al registrar al usuario, por favor intente nuevamente.',
            ];
        }
    }

    public function checkEmailExists(string $email)
    {
        $email = mysqli_real_escape_string($this->connection, $email);

        $query = "SELECT * FROM $this->table_user WHERE correo = '$email'";

        $result = mysqli_query($this->connection, $query);

        if (mysqli_num_rows($result) > 0) {
            return true;
        }

        return false;
    }

    public function sendMail(string $email, string $token)
    {
    }

    public function logout()
    {
    }
}
