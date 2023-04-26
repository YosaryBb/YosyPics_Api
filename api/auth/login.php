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

    $response = $auth->login($input);
    echo Utils::response($response, 200);
} catch (\Throwable $th) {
    echo Utils::response([
        'status' => false,
        'message' => $th->getMessage()
    ], 500);
}
