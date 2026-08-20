<?php
namespace Helpers;

class Response {
    public static function json($data, int $statusCode = 200): void {
        if (ob_get_length()) {
            ob_clean(); // Clear any pre-buffered output or PHP notices
        }
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    public static function success($data = [], string $message = "Success", int $statusCode = 200): void {
        self::json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    public static function error(string $message = "Error", array $errors = [], int $statusCode = 400): void {
        self::json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }
}