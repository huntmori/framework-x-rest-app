<?php

namespace Damoyo\Api\Domain\User\Service;

use Damoyo\Api\Domain\User\Repository\UserRepository;

class UserServiceImpl implements UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function listUsers(): array
    {
        return $this->userRepository->find();
    }

    public function createUser(array $userData): array
    {
        // 실제 구현에서는 데이터베이스에 저장하는 로직이 들어갈 것입니다.
        return array_merge(['id' => 3], $userData);
    }
}
