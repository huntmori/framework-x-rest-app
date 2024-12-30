<?php

namespace Damoyo\Api\Common\Routing;

use DI\Container;
use FrameworkX\App;
use ReflectionClass;
use ReflectionMethod;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Damoyo\Api\Common\Dto\ResponseDto;

class AttributeRouter
{
    private App $app;
    private Container $container;

    public function __construct(App $app, Container $container)
    {
        $this->app = $app;
        $this->container = $container;
    }

    private function convertToResponse(ResponseDto $dto, string $responseType): Response
    {
        $headers = ['Content-Type' => $responseType];
        $body = '';

        switch ($responseType) {
            case 'application/json':
                $body = json_encode($dto);
                break;
            case 'text/xml':
                // XML 변환 로직 추가
                $body = $this->convertToXml($dto);
                break;
            // 다른 Content-Type에 대한 처리 추가
        }

        return new Response(200, $headers, $body);
    }

    private function convertToXml(ResponseDto $dto): string
    {
        // XML 변환 로직 구현
        // ...
        return $xmlString;
    }

    public function registerController(string $controllerClass): void
    {
        $reflection = new ReflectionClass($controllerClass);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) 
        {
            $attributes = $method->getAttributes(Route::class);
            if (empty($attributes)) {
                continue;
            }

            foreach ($attributes as $attribute) 
            {
                $route = $attribute->newInstance();
                $path = $route->path;
                $httpMethod = strtolower($route->method->value);
                $responseType = $route->responseType ?? 'application/json'; // 기본 응답 유형 설정

                $this->app->$httpMethod($path, function (ServerRequestInterface $request) use ($controllerClass, $method, $responseType) {
                    try {
                        $controller = $this->container->get($controllerClass);
                        $result = $controller->{$method->getName()}($request);
                        
                        if ($result instanceof ResponseDto) {
                            return $this->convertToResponse($result, $responseType);
                        }
                        
                        return $result;
                    } catch (\Throwable $e) {
                        $errorResponse = new ResponseDto();
                        $errorResponse->code = 500;
                        $errorResponse->result = false;
                        $errorResponse->message = 'Internal Server Error';
                        $errorResponse->data = ['error' => $e->getMessage()];
                        
                        return $this->convertToResponse($errorResponse, $responseType);
                    }
                });
            }
        }
    }

    public function registerControllersFromDirectory(string $directory): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $className = $this->getClassNameFromFile($file->getPathname());
                if ($className && str_ends_with($className, 'Controller')) {
                    $this->registerController($className);
                }
            }
        }
    }

    private function getClassNameFromFile(string $file): ?string
    {
        $content = file_get_contents($file);
        if (preg_match('/namespace\s+(.+?);/s', $content, $matches)) {
            $namespace = $matches[1];
            if (preg_match('/class\s+(\w+)/', $content, $matches)) {
                return $namespace . '\\' . $matches[1];
            }
        }
        return null;
    }
}
