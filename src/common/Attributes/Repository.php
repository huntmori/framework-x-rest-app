<?php

namespace Src\common\Attributes;

#[\Attribute] #[Attribute(Attribute::TARGET_CLASS)]
class Repository
{
    public function __construct(
        public string $interface,
        public string $implement
    )
    {

    }
}