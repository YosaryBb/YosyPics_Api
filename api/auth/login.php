<?php

require_once '../../utils/utils.php';
require_once '../../models/auth.php';
require_once '../../connection/connection.php';

use utils\Utils;
use models\Auth;
use connection\Connection;

Utils::headers();

$connection = new Connection();

$auth = new Auth($connection->getConnection());

$email = "joshua15mclean@gmail.com";
$password = "admin123";

// $response = $auth->login($email, $password);

$response = $auth->user('QE0gAlW8E59_hmQFMiCvThEX4r1W5PBlViWPtZhceyplnma53Y5nssCqUld7OpZt');

echo Utils::response($response, 200);

// echo json_encode($response);
