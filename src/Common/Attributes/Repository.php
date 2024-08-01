<?php

namespace Src\Common\Attributes;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Repository
{
    public function __construct(
        public string $interface,
        public string $implement
    )
    {

    }
}