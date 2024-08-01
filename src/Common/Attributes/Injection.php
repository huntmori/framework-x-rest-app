<?php

namespace Src\Common\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Injection
{
    public function __construct(
        public string $interfaceName,
        public string $implementName,
        public array $arguments
    ){}
}