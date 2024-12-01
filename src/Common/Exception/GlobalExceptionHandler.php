<?php

namespace Damoyo\Api\Common\Exception;

use Damoyo\Api\Common\Dto\ResponseDto;
use Exception;
use Throwable;

class GlobalExceptionHandler
{
    public function handle(Throwable $exception): ResponseDto
    {
        // 기본 에러 응답 설정
        $statusCode = 500;
        $message = '서버 오류가 발생했습니다.';
        echo <<<LOG
            file : {$exception->getFile()}
            line : {$exception->getLine()}
            message : {$exception->getMessage()}
            =================================================
        LOG;

        // 예외 타입에 따른 처리
        if ($exception instanceof ValidationException) {
            $statusCode = 400;
            $message = $exception->getMessage();
        } elseif ($exception instanceof NotFoundException) {
            $statusCode = 404;
            $message = $exception->getMessage();
        } elseif ($exception instanceof UnauthorizedException) {
            $statusCode = 401;
            $message = $exception->getMessage();
        }

        // 개발 환경에서만 상세 에러 정보 포함
        $debug = [];
        if (getenv('APP_ENV') === 'development') {
            $debug = [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
        }

        return ResponseDto::init()
            ->code($statusCode)
            ->result(false)
            ->message($exception->getMessage())
            ->data($debug);
    }
}
