<?php

namespace Damoyo\Api\Common\Common\Exception;

use FrameworkX\ErrorHandler;
use React\Http\Message\Response;
use Throwable;

class Handler extends ErrorHandler
{
    public function handle(Throwable $e): Response
    {
        if ($e instanceof NotFoundException) {
            return new Response(
                404,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage() ?: 'Resource not found',
                    'code' => 404
                ])
            );
        }

        // 기본 서버 에러 응답
        return new Response(
            500,
            ['Content-Type' => 'application/json'],
            json_encode([
                'status' => 'error',
                'message' => 'Internal Server Error',
                'code' => 500
            ])
        );
    }
}
