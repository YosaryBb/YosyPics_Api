<?php

require_once __DIR__ . '/../../utils/utils.php';
require_once __DIR__ . '/../../models/auth.php';

use utils\Utils;
use models\Auth;

Utils::headers();

try {
    if (!Utils::validateRequestMethod('DELETE')) {
        Utils::responseMethodNotAllowed();
        exit();
    }

    if (Utils::getTokenFromHeader() === null) {
        Utils::unauthenticated();
        exit();
    }

    $auth = new Auth();

    echo Utils::response($auth->logout());
} catch (\Throwable $th) {
    echo Utils::response([
        'status' => false,
        'message' => $th->getMessage()
    ], 500);
}
