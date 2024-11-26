<?php

namespace Damoyo\Api\Domain\User\Service;

class UserServiceImpl implements UserService
{
    public function listUsers(): array
    {
        return [
            ['id' => 1, 'name' => '홍길동'],
            ['id' => 2, 'name' => '김철수']
        ];
    }

    public function createUser(array $userData): array
    {
        // 실제 구현에서는 데이터베이스에 저장하는 로직이 들어갈 것입니다.
        return array_merge(['id' => 3], $userData);
    }
}
