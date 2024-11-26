<?php
namespace Damoyo\Api\Common\Routing;

use Attribute;
use Damoyo\Api\Common\Routing\HttpMethod;

#[Attribute(Attribute::TARGET_METHOD)]
class Route {
    public function __construct(
        public string $path,
        public HttpMethod $method = HttpMethod::GET
    ) {}
}
