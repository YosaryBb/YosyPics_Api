<?php

require_once __DIR__ . '/../../utils/utils.php';
require_once __DIR__ . '/../../models/auth.php';

use utils\Utils;
use models\Auth;

Utils::headers();

try {
    if (!Utils::validateRequestMethod('POST')) {
        Utils::responseMethodNotAllowed();
        exit();
    }

    $input = Utils::getContents();

    $errors = Utils::validate($input, [
        'correo' => ['required', 'email'],
        'password' => ['required'],
        'nombre' => ['required'],
        'apellido' => ['required'],
    ]);

    if (!empty($errors)) {
        echo Utils::response([
            'status' => false,
            'message' => $errors
        ]);
        exit();
    }

    $auth = new Auth();
    if ($auth->checkEmailExists($input['correo'])) {
        echo Utils::response([
            'status' => false,
            'message' => 'El correo electrónico ya está registrado, por favor intente con otro correo electrónico.'
        ]);
        exit();
    }

    $response = $auth->register($input);
    echo Utils::response($response, 200);
} catch (\Throwable $th) {
    echo Utils::response([
        'status' => false,
        'message' => $th->getMessage()
    ], 500);
}
