<?php

namespace models;

require_once '../../utils/utils.php';

use utils\Utils;

class auth
{
    protected $connection;
    protected $table_user = 'usuario';
    protected $table_token = 'token_acceso';
    protected $table_profile = 'perfiles';

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function login($email, $password)
    {
        $email = mysqli_real_escape_string($this->connection, $email);
        $password = mysqli_real_escape_string($this->connection, $password);
        $password = Utils::hash($password);

        $query = "SELECT * FROM $this->table_user WHERE correo = '$email' AND password = '$password'";

        $result = mysqli_query($this->connection, $query);

        if (mysqli_num_rows($result) > 0) {
            return [
                'status' => true,
                'message' => 'Login realizado con éxito',
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

    public function logout()
    {
    }

    public function user($token = null)
    {
        $query = "SELECT * FROM $this->table_token WHERE token = '$token'";

        $result = mysqli_query($this->connection, $query);

        if (mysqli_num_rows($result) > 0) {
            $id_user = mysqli_fetch_assoc($result)['id_usuario'];

            $query = "SELECT id, nombre, apellido, imagen FROM $this->table_profile WHERE id_usuario = '$id_user'";

            $result = mysqli_query($this->connection, $query);

            if (mysqli_num_rows($result) > 0) {
                return mysqli_fetch_assoc($result);
            } else {
                return null;
            }
        }
    }
}
