<?php

namespace utils;

require_once __DIR__ . '/constants.php';

use Exception;
use InvalidArgumentException;

class Utils extends Constants
{
    public static function getRandomString(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public static function getRandomInt(int $min = 1, int $max = 10): int
    {
        return rand($min, $max);
    }

    public static function baseUrl(): string
    {
        return dirname(__DIR__, 1) . '/';
    }

    public static function createStoragePath()
    {
        if (!file_exists(self::STORAGE_PATH_NAME)) {
            return mkdir(self::STORAGE_PATH_NAME, 0755);
        }
    }

    public static function storagePath(): string
    {
        self::createStoragePath();
        return self::baseUrl() . self::STORAGE_PATH_NAME . '/';
    }

    public static function uploadFile($path = "", $file)
    {
        $allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!in_array($file_extension, $allowed_extensions)) {
            return http_response_code(400);
        }

        $upload_folder = self::storagePath() . "/" . $path;
        if (!is_writable($upload_folder)) {
            return http_response_code(500);
        }

        move_uploaded_file($file['tmp_name'], $upload_folder . '/' . $file['name']);
        return http_response_code(200);
    }

    public static function deleteFile($filename)
    {
        $file_path = self::storagePath() . $filename;
        if (!file_exists($file_path)) {
            return http_response_code(404);
        }

        unlink($file_path);
        return http_response_code(200);
    }

    public static function downloadFile($filename)
    {
        $file_path = self::storagePath() . $filename;
        if (!file_exists($file_path)) {
            return http_response_code(404);
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    }

    public static function createToken(int $length = 64): string
    {
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-';
        $token = '';
        $max = mb_strlen($keyspace, '8bit') - 1;

        for ($i = 0; $i < $length; ++$i) {
            $token .= $keyspace[random_int(0, $max)];
        }

        return $token;
    }

    public static function hash(string $string): string
    {
        return hash_hmac(self::HASH_ALGORITHM, $string, self::HASH_KEY);
    }

    public static function fetch($url, $method = 'GET', $data = null, $headers = array()): mixed
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                $headers[] = 'Content-Type: application/json';
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            if ($response === false) {
                throw new Exception(curl_error($ch));
            }

            curl_close($ch);

            return $response;
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public static function parseResponse(mixed $response): mixed
    {
        $json = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error parsing response: ' . json_last_error_msg());
        }

        return $json ?? [];
    }

    public static function handleErrors($response): mixed
    {
        if (isset($response->error)) {
            throw new Exception('API error: ' . $response->error);
        }
        if (isset($response->errors)) {
            $error_msg = '';
            foreach ($response->errors as $field => $message) {
                $error_msg .= "$field: $message\n";
            }
            throw new Exception('Validation error: ' . $error_msg);
        }
        return $response;
    }

    public static function paginate($results, $page = 1, $page_size = 10): array
    {
        if (!is_array($results)) {
            throw new InvalidArgumentException('$results debe ser una matriz.');
        }

        $total_results = count($results);
        $total_pages = ceil($total_results / $page_size);

        if ($page < 1 || $page > $total_pages) {
            throw new InvalidArgumentException('$page debe estar dentro del rango válido.');
        }

        if ($page_size <= 0) {
            throw new InvalidArgumentException('$page_size debe ser un número positivo.');
        }

        $start_index = ($page - 1) * $page_size;
        $end_index = min($start_index + $page_size, $total_results);
        $paged_results = array_slice($results, $start_index, $end_index - $start_index);

        return array(
            'total_results' => $total_results,
            'total_pages' => $total_pages,
            'results' => $paged_results
        );
    }

    public static function response($data = [], $status_code = 200): string
    {
        if (!is_array($data)) {
            return null;
        }

        http_response_code($status_code);
        return json_encode($data);
    }

    public static function isDataValid($data): bool
    {
        if (!is_array($data)) {
            return true;
        }

        foreach ($data as $key => $value) {
            if ($value === null || (is_string($value) && trim($value) === '')) {
                return true;
            }
        }

        foreach ($data as $key => $value) {
            if (is_string($value) && preg_match('/<\s*script\s*>.*<\s*\/\s*script\s*>/i', $value)) {
                return true;
            }
        }

        return false;
    }

    public static function timestamps(): string
    {
        return date('Y-m-d H:i:s');
    }

    public static function headers(): void
    {
        header('Content-Type: ' . self::HEADER_CONTENT_TYPE);
        header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
        header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept, ' . self::AUTHORIZATION_HEADER_NAME);
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('X-Content-Type-Options: ' . self::HEADER_XCTO);
        header('ETag: "' . self::hash(self::createToken()) . '"');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
        header('Retry-After: ' . self::RETRY_AFTER);
        header('X-RateLimit-Limit: ' . self::RATE_LIMIT);
        header('X-RateLimit-Remaining: ' . self::RATE_LIMIT_REMAINING);
    }

    public static function getContents(): mixed
    {
        if (self::validateRequestMethod('POST') && !empty($_POST)) {
            return $_POST;
        }

        $body = file_get_contents('php://input');

        if ($body === false || $body === '') {
            return [];
        }

        return self::parseResponse($body);
    }

    public static function hasFile(string $file_name): bool
    {
        return isset($_FILES[$file_name]) && !empty($_FILES[$file_name]['name']);
    }

    public static function getFile(string $file_name): mixed
    {
        if (isset($_FILES[$file_name])) {
            $file = $_FILES[$file_name];
            if ($file['error'] === UPLOAD_ERR_OK) {
                return [
                    'name' => $file['name'],
                    'type' => $file['type'],
                    'size' => $file['size'],
                    'tmp_name' => $file['tmp_name']
                ];
            }
        }
        return null;
    }

    public static function getTokenFromHeader(): ?string
    {
        return isset(getallheaders()[self::AUTHORIZATION_HEADER_NAME]) ?
            getallheaders()[self::AUTHORIZATION_HEADER_NAME] :
            null;
    }

    public static function unauthenticated(): void
    {
        http_response_code(401);
        echo Utils::response([
            'status' => false,
            'message' => 'Acceso denegado'
        ], 401);
    }

    public static function validate($data = [], $rules): array
    {
        $errors = array();

        foreach ($rules as $field => $rules_field) {
            if (!isset($data[$field])) {
                $errors[$field] = 'El campo ' . $field . ' es requerido';
                continue;
            }

            if (in_array('required', $rules_field) && empty($data[$field])) {
                $errors[$field] = 'El campo ' . $field . ' es requerido';
            }

            if (in_array('email', $rules_field) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = 'El campo debe ser un correo electrónico válido';
            }

            if (isset($rules_field['min']) && strlen($data[$field]) < $rules_field['min']) {
                $errors[$field] = 'El campo ' . $field . ' debe tener al menos ' . $rules_field['min'] . ' caracteres';
            }

            if (isset($rules_field['max']) && strlen($data[$field]) > $rules_field['max']) {
                $errors[$field] = 'El campo ' . $field . ' no puede tener más de ' . $rules_field['max'] . ' caracteres';
            }

            if (in_array('image', $rules_field)) {
                if (!isset($data[$field]['tmp_name'])) {
                    $errors[$field] = 'El campo debe ser una imagen';
                } else {
                    $image_info = getimagesize($data[$field]['tmp_name']);
                    $mime_type = $image_info['mime'];
                    if (!in_array($mime_type, ['image/jpeg', 'image/png', 'image/gif'])) {
                        $errors[$field] = 'El campo debe ser una imagen de tipo JPEG, PNG o GIF';
                    }
                }
            }
        }

        return $errors;
    }

    public static function validateRequestMethod(string $method = "GET"): bool
    {
        return $_SERVER['REQUEST_METHOD'] === $method;
    }

    public static function responseMethodNotAllowed(): void
    {
        http_response_code(405);
        echo Utils::response([
            'status' => false,
            'message' => 'Método no permitido'
        ], 405);
    }
}
