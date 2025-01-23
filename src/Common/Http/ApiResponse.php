<?php

namespace Damoyo\Api\Common\Http;

use React\Http\Message\Response;

class ApiResponse
{
    public static function json(mixed $data, int $status = 200, array $headers = []): Response
    {
        $defaultHeaders = [
            'Content-Type' => 'application/json',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'deny'
        ];

        $responseData = [
            'success' => $status >= 200 && $status < 300,
            'data' => $data
        ];

        return new Response(
            $status,
            array_merge($defaultHeaders, $headers),
            json_encode($responseData, JSON_UNESCAPED_UNICODE)
        );
    }

    public static function error(string $message, int $status = 400, array $errors = [], array $headers = []): Response
    {
        $defaultHeaders = [
            'Content-Type' => 'application/json',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'deny'
        ];

        $responseData = [
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ];

        return new Response(
            $status,
            array_merge($defaultHeaders, $headers),
            json_encode($responseData, JSON_UNESCAPED_UNICODE)
        );
    }
}
