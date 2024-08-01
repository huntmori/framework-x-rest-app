<?php

namespace Src\User\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use React\Http\Message\Response as Response;

interface UserController
{
    public function create(Request $request): Response;
    public function update(Request $request): Response;
    public function delete(Request $request): Response;
    public function getOne(Request $request): Response;
    public function getList(Request $request): Response;
}