<?php

namespace Src\Common\Attributes;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Service
{
    public function __construct
    (
        public string $interface,
        public string $implement
    )
    {}

    public function test(){}
}