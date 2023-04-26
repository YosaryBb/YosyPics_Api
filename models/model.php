<?php

namespace models;

require_once '../../utils/utils.php';
require_once '../../connection/connection.php';

use utils\Utils;
use connection\Connection;

class Model
{
    protected $connection;
    protected $table_user = 'usuario';
    protected $table_token = 'token_acceso';
    protected $table_profile = 'perfiles';

    public function __construct()
    {
        $connectionInstance = new Connection();
        $this->connection = $connectionInstance->getConnection();
    }

    public function authUser()
    {
        $token = Utils::getTokenFromHeader();

        if ($token === null) {
            return null;
        }

        $sentence = "SELECT usuario.id, usuario.correo, perfiles.nombre, perfiles.apellido, perfiles.imagen, usuario.verificado, usuario.estado FROM $this->table_token INNER JOIN $this->table_user ON token_acceso.id_usuario = usuario.id INNER JOIN $this->table_profile ON usuario.id = perfiles.id_usuario WHERE token_acceso.token = '$token'";

        $result = mysqli_query($this->connection, $sentence);

        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);

            if (!Utils::isDataValid($data)) {
                return null;
            }

            $this->connection->close();

            return $data;
        }

        $this->connection->close();
        return null;
    }

    public function isProtected(): bool
    {
        $token = Utils::getTokenFromHeader();

        if ($token === null) {
            return false;
        }

        $sentence = "SELECT * FROM $this->table_token WHERE token = '$token'";

        $result = mysqli_query($this->connection, $sentence);

        if (mysqli_num_rows($result) > 0) {
            $this->connection->close();
            return true;
        }

        $this->connection->close();
        return false;
    }
}
