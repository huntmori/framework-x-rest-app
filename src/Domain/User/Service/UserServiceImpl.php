<?php

namespace Damoyo\Api\Domain\User\Service;

use Damoyo\Api\Domain\User\Dto\UserCreate\UserCreateRequest;
use Damoyo\Api\Domain\User\Repository\UserRepository;
use Damoyo\Api\Domain\User\Entity\User;

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

    public function createUser(UserCreateRequest $userData): int
    {
        /** @var User $user */ 
        $user = User::init()
            ->setId($userData->id)
            ->setEmail($userData->email)
            ->setPassword($userData->password)
            ->build(); 
        return $this->userRepository->save($user);
    }
    
    public function findOneById(int $id): ?User {
        return $this->userRepository->findOneById((string)$id);
    }

    public function getUserByUid(string $uid): ?User
    {
        return $this->userRepository->findByUid((string)$uid);
    }
}
