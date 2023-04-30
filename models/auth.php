<?php

namespace models;

require_once __DIR__ . "../../utils/utils.php";
require_once __DIR__ . "/model.php";
require_once __DIR__ . "../../utils/mail.php";

use utils\Utils;
use models\Model;
use utils\Mail;

class auth extends Model
{
    protected $table_user = 'usuario';
    protected $table_token = 'token_acceso';
    protected $table_profile = 'perfiles';
    protected $table_forgot = 'token_recuperacion';

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
                'token' => $this->saveLoginToken(mysqli_fetch_assoc($result)['id']),
            ];
        }

        return [
            'status' => false,
            'message' => 'El correo electrónico o la contraseña son incorrectos',
        ];
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

                $mail = new Mail();

                $mail->sendConfirmation($email, "Confirmación de cuenta", [
                    'name' => $name,
                    'token' => $verifyToken
                ]);

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

    public function forgot($input): array
    {
        $email = mysqli_real_escape_string($this->connection, $input['correo']);

        if ($this->checkEmailExists($email)) {
            $recovery_token = Utils::createToken();
            $date = Utils::timestamps();

            $query = "INSERT INTO $this->table_forgot (correo, token, fecha) VALUES ('$email', '$recovery_token', '$date')";

            $result = mysqli_query($this->connection, $query);

            if ($result) {

                $mail = new Mail();
                $mail->sendForgot($email, "Restablecer contraseña", [
                    'name' => $input['correo'],
                    'token' => $recovery_token
                ]);

                return [
                    'status' => true,
                    'message' => 'Se ha enviado un correo para restablecer tu contraseña. Revisa tu bandeja de entrada.'
                ];
            }

            return [
                'status' => false,
                'message' => 'Ah ocurrido un error, por favor intente nuevamente.'
            ];
        }

        return [
            'status' => false,
            'message' => 'El correo electrónico no existe.'
        ];
    }

    public function resetPassword($input)
    {
        $email = mysqli_real_escape_string($this->connection, $input['correo']);
        $token = mysqli_real_escape_string($this->connection, $input['token']);
        $password = mysqli_real_escape_string($this->connection, $input['password']);
        $password = Utils::hash($password);

        $query = "SELECT * FROM $this->table_forgot WHERE correo = '$email' AND token = '$token'";

        $result = mysqli_query($this->connection, $query);

        if (mysqli_num_rows($result) > 0) {
            $query = "UPDATE $this->table_user SET password = '$password' WHERE correo = '$email'";

            $result = mysqli_query($this->connection, $query);

            if ($result) {
                $query = "UPDATE $this->table_forgot SET token = null WHERE correo = '$email' AND token = '$token'";
                mysqli_query($this->connection, $query);

                $mail = new Mail();

                $mail->sendNotification($email, "Cambio de contraseña", [
                    'message' => 'Su contraseña ha sido cambiada con éxito. El correo electrónico es: ' . $email
                ]);

                return [
                    'status' => true,
                    'message' => 'Contraseña actualizada con éxito.'
                ];
            } else
                return [
                    'status' => false,
                    'message' => 'Ah ocurrido un error, por favor intente nuevamente.'
                ];
        } else
            return [
                'status' => false,
                'message' => 'Ah ocurrido un error, por favor verifique si los datos enviados son correctos.'
            ];
    }

    public function changePassword($input): array
    {
        $email = mysqli_real_escape_string($this->connection, $input['correo']);
        $currentPassword = mysqli_real_escape_string($this->connection, $input['currentPassword']);
        $newPassword = mysqli_real_escape_string($this->connection, $input['newPassword']);

        $currentPassword = Utils::hash($currentPassword);
        $newPassword = Utils::hash($newPassword);

        if ($this->checkIsCurrentPasswordIsValid($email, $currentPassword)) {
            $query = "UPDATE $this->table_user SET password = '$newPassword' WHERE correo = '$email'";

            $result = mysqli_query($this->connection, $query);

            if ($result) {
                return [
                    'status' => true,
                    'message' => 'Contraseña actualizada con éxito.'
                ];
            }

            return [
                'status' => false,
                'message' => 'Ah ocurrido un error, por favor intente nuevamente.'
            ];
        } else {
            return [
                'status' => false,
                'message' => 'La contraseña actual es incorrecta.'
            ];
        }
    }

    public function logout()
    {
        $token = Utils::getTokenFromHeader();

        $query = "DELETE FROM $this->table_token WHERE token = '$token'";

        $result = mysqli_query($this->connection, $query);

        if ($result) {
            return [
                'status' => true,
                'message' => 'La sesión ha sido cerrada con éxito.'
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Ah ocurrido un error, por favor intente nuevamente.'
            ];
        }
    }

    public function verifyAccount($input)
    {
        $token = mysqli_real_escape_string($this->connection, $input['token']);

        $query = "SELECT * FROM $this->table_user WHERE token_verificacion = '$token'";

        $result = mysqli_query($this->connection, $query);

        if (mysqli_num_rows($result) > 0) {
            $query = "UPDATE $this->table_user SET token_verificacion = null, verificado = 1 WHERE token_verificacion = '$token'";

            $result = mysqli_query($this->connection, $query);

            if ($result) {
                return [
                    'status' => true,
                    'message' => 'Cuenta verificada con éxito.'
                ];
            }

            return [
                'status' => false,
                'message' => 'Ah ocurrido un error, por favor intente nuevamente.'
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Vaya ah ocurrido un error, por favor intente nuevamente.'
            ];
        }
    }
}
