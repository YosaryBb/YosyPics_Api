<?php

namespace utils;

use Exception;

class Utils
{
    const STORAGE_PATH_NAME = 'storage';
    const HASH_KEY = '123456789';
    const HASH_ALGORITHM = 'sha256';

    private static $allowed_origins = array(
        'http://localhost:8080',
        'http://localhost:3000',
        'http://localhost:80',
    );

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

    public static function getRandomInt(int $min, int $max): int
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

    public static function createToken(): string
    {
        $length = 64;
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
        if (!$json || isset($json->error)) {
            // throw new Exception('Error parsing response: ' . json_last_error_msg());
            return [];
        }
        return $json;
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

    public static function paginate($results, $page = 1, $page_size = 10)
    {
        $total_results = count($results);
        $total_pages = ceil($total_results / $page_size);
        $start_index = ($page - 1) * $page_size;
        $end_index = min($start_index + $page_size, $total_results);
        $paged_results = array_slice($results, $start_index, $end_index - $start_index);

        return array(
            'total_results' => $total_results,
            'total_pages' => $total_pages,
            'results' => $paged_results
        );
    }

    public static function response($data = [], $status_code = 200)
    {
        if (self::isDataValid($data)) {
            return http_response_code(400);
        }

        http_response_code($status_code);
        return json_encode($data);
    }

    public static function isDataValid($data)
    {
        if (!is_array($data)) {
            return false;
        }

        foreach ($data as $key => $value) {
            if (empty($value) && $value !== 0) {
                return false;
            }
        }

        foreach ($data as $key => $value) {
            if (is_string($value) && preg_match('/<\s*script\s*>.*<\s*\/\s*script\s*>/', $value)) {
                return false;
            }
        }

        return true;
    }


    public static function timestamps(): string
    {
        return date('Y-m-d H:i:s');
    }

    public static function headers()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: ' . self::$allowed_origins[0]);
        header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('X-Content-Type-Options: nosniff');
    }

    public static function getContents()
    {
        return self::parseResponse(file_get_contents('php://input'));
    }
}
