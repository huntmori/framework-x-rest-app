<?php

namespace Src\Common\Attributes;

use Attribute;
use FrameworkX\App;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public string $className;
    public string $targetMethodName;
    public string $method;
    public string $path;

    public const string
        GET = 'GET',
        HEAD = 'HEAD',
        POST = 'POST',
        PUT = 'PUT',
        PATCH = 'PATCH',
        DELETE = 'DELETE',
        OPTIONS = 'OPTIONS',
        ANY = 'ANY',
        REDIRECT = 'REDIRECT';

    public function __construct(
      string $method,
      string $path
    ) {
        $this->path = $path;
        $this->method = $method;
        $this->targetMethodName = "";
        $this->className = "";
    }

    public function setClassInfo(string $className, string $methodName): void
    {
        $this->className = $className;
        $this->targetMethodName = $methodName;
    }
}