<?php
namespace Damoyo\Api\Common\Common\Routing;

use Attribute;
use Damoyo\Api\Common\Routing\ResponseDto;

#[Attribute(Attribute::TARGET_METHOD)]
class Route {
    public function __construct(
        public string $path,
        public HttpMethod $method = HttpMethod::GET,
        public string $responseType = ResponseDto::class
    ) {}
}
