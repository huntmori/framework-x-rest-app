<?php
namespace Damoyo\Api\Common\Common\Routing;

enum HttpMethod: string {
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case PATCH = 'PATCH';
    case HEAD = 'HEAD';
    case OPTIONS = 'OPTIONS';

    public function equals(string $method): bool {
        return $this->value === strtoupper($method);
    }

    public static function fromString(string $method): self {
        return self::tryFrom(strtoupper($method)) ?? throw new \ValueError("Invalid HTTP method: $method");
    }
}
