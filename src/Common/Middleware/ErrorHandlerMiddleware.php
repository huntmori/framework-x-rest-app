<?php

namespace Damoyo\Api\Common\Middleware;

use Damoyo\Api\Common\Dto\ResponseDto;
use Damoyo\Api\Common\Logger\AppLogger;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Throwable;

class ErrorHandlerMiddleware
{
    private AppLogger $logger;

    public function __construct()
    {
        $this->logger = AppLogger::getInstance();
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        try {
            $response = $next($request);
            
            // 404 에러 처리
            if ($response instanceof Response && $response->getStatusCode() === 404) {
                $path = $request->getUri()->getPath();
                $this->logger->warning('404 Not Found: ' . $path);
                
                return ResponseDto::toResponse(
                    ResponseDto::init()
                        ->result(false)
                        ->code(404)
                        ->message('요청하신 리소스를 찾을 수 없습니다.')
                        ->data(['path' => $path])
                );
            }
            
            return $response;
        } catch (Throwable $e) {
            $this->logger->error('Error handling request: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return ResponseDto::toResponse(
                ResponseDto::init()
                    ->result(false)
                    ->code(500)
                    ->message('서버 내부 오류가 발생했습니다.')
            );
        }
    }
}
