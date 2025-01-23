<?php

namespace Damoyo\Api\Common\Http;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class RequestMapper
{
    public string $mapperClass;
    public string $mapperMethod;

    public function __construct(string $mapperClass, string $mapperMethod)
    {
        $this->mapperClass = $mapperClass;
        $this->mapperMethod = $mapperMethod;
    }
}