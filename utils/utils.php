<?php

namespace utils;

use Exception;

class Utils
{
    const STORAGE_PATH_NAME = 'storage';
    const HASH_KEY = '123456789';
    const HASH_ALGORITHM = 'sha256';

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

    public static function uploadFile(string $file_name, string $file_path): bool
    {
        return move_uploaded_file($file_path, self::storagePath() . $file_name);
    }

    public static function deleteFile(string $file_name): bool
    {
        return unlink(self::storagePath() . $file_name);
    }

    public static function downloadFile($filepath, $filename): void
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));

        readfile(self::storagePath() . $filepath);
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
        $json = json_decode($response);
        if (!$json || isset($json->error)) {
            throw new Exception('Error parsing response: ' . json_last_error_msg());
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
}
