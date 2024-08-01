<?php

namespace Src\Common\Attributes;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Controller
{
    public function __construct
    (
        public string $interface,
        public string $implement
    )
    {

    }
}