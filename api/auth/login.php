<?php

header('Content-Type: application/json');

echo json_encode([
    'status' => 'success',
    'data' => [
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'W6qKf@example.com',
    ]
]);
