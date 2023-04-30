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

    $input = Utils::getContents();

    $auth = new Auth();
    $response = $auth->verifyAccount($input);

    echo Utils::response($response);
} catch (\Throwable $th) {
    echo Utils::response([
        'status' => false,
        'message' => $th->getMessage()
    ], 500);
}
