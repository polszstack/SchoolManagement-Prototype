<?php
/**
 * Response
 * Static helpers for sending JSON API responses.
 */
class Response
{
    public static function json(mixed $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        header('X-Content-Type-Options: nosniff');
        echo json_encode($data);
        exit;
    }

    public static function success(string $message, array $extra = []): never
    {
        self::json(array_merge(['success' => true, 'message' => $message], $extra));
    }

    public static function error(string $message, int $code = 400): never
    {
        self::json(['error' => $message], $code);
    }
}