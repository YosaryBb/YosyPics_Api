<?php

require_once __DIR__ . '/../../utils/utils.php';
require_once __DIR__ . '/../../models/auth.php';

use utils\Utils;
use models\Auth;

Utils::headers();

try {
    if (!Utils::validateRequestMethod('GET')) {
        Utils::responseMethodNotAllowed();
        exit();
    }

    if (Utils::getTokenFromHeader() === null) {
        Utils::unauthenticated();
        exit();
    }

    $auth = new Auth();

    $user = $auth->authUser();

    if ($user !== null) {
        echo Utils::response([
            'status' => true,
            'user' => $user
        ], 200);
        exit();
    }

    echo Utils::response([
        'status' => false,
        'message' => 'El token no es valido.'
    ], 404);
} catch (\Throwable $th) {
    echo Utils::response([
        'status' => false,
        'message' => $th->getMessage()
    ], 500);
}
