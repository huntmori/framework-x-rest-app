<?php

namespace Src\common;

use FrameworkX\App;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response as Response;
use Src\common\Attributes\Controller;

#[Controller(TestController::class, TestController::class)]
class TestController
{
    private TestRepository $testRepository;
    private App $app;
    public $home;

    public function __construct(TestRepository $testRepository)
    {
        $this->testRepository = $testRepository;
    }

    public static function home(ServerRequestInterface $request): Response
    {
        return Response::plaintext(
            "Hello " . $request->getAttribute('name') . "!\n"
        );
    }


}