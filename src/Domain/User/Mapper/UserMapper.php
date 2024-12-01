<?php

namespace Damoyo\Api\Domain\User\Mapper;

use Damoyo\Api\Domain\User\Dto\UserCreateRequest;

class UserMapper
{
    public function toUserCreateRequest(array $data): UserCreateRequest
    {
        $request = new UserCreateRequest();
        $request->id = $data['id'] ?? null;
        $request->email = $data['email'] ?? null;
        $request->password = $data['password'] ?? null;
        
        return $request;
    }
}