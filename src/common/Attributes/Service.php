<?php

namespace Src\common\Attributes;

#[Attribute(Attribute::TARGET_CLASS)]
class Service
{
    public function __construct
    (
        public string $interface,
        public string $implement
    )
    {
    }

    public function test(){}
}