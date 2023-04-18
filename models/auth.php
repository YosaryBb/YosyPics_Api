<?php

namespace models;

include_once "../autoload.php";

use utils\Utils;

class auth
{
    protected $connection;
    protected $table_user = '';
    protected $table_token = '';

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function login($email, $password)
    {
        $email = mysqli_real_escape_string($this->connection, $email);
        $password = mysqli_real_escape_string($this->connection, $password);
        $password = Utils::hash($password);

        $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";

        $result = mysqli_query($this->connection, $query);

        if (mysqli_num_rows($result) > 0) {
            return [
                'status' => true,
                'message' => 'Login realizado com sucesso',
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
            $query = "UPDATE users SET token = '$token' WHERE id = '$id'";
            mysqli_query($this->connection, $query);
            return $token;
        }

        return null;
    }
}
