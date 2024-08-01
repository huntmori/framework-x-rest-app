<?php

namespace Src\User;

use Psr\Http\Message\ServerRequestInterface;
use Src\User\Dto\UserDto;

class UserRequestBinder
{

    public function bind(ServerRequestInterface $request): UserDto
    {
        $body = $request->getParsedBody();

        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';

        return new UserDto($name, $email);
    }
}