<?php
namespace Damoyo\Api\Common\Routing;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route {
    public function __construct(
        public string $path,
        public HttpMethod $method = HttpMethod::GET,
        public string $responseType = ResponseDto::class
    ) {}
}
