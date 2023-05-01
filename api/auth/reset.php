<?php

require_once __DIR__ . '/../../utils/utils.php';
require_once __DIR__ . '/../../models/auth.php';

use utils\Utils;
use models\Auth;

Utils::headers();

try {
    if (!Utils::validateRequestMethod('PUT')) {
        Utils::responseMethodNotAllowed();
        exit();
    }

    $input = Utils::getContents();

    if ($input['token'] === "" || $input['token'] === null) {
        echo Utils::response([
            'status' => false,
            'message' => 'Al parecer a ocurrido un error, vuelve a intentarlo más tarde.'
        ]);
        exit();
    }

    $auth = new Auth();

    $errors = Utils::validate($input, [
        'correo' => ['required', 'email'],
        'password' => ['required']
    ]);

    if (!empty($errors)) {
        echo Utils::response([
            'status' => false,
            'message' => $errors
        ]);
        exit();
    }

    $response = $auth->resetPassword($input);

    echo Utils::response($response);
} catch (\Throwable $th) {
    echo Utils::response([
        'status' => false,
        'message' => $th->getMessage()
    ], 500);
}
