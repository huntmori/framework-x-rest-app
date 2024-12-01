<?php

namespace Damoyo\Api\Domain\User\Service;

use Damoyo\Api\Domain\User\Dto\UserCreateRequest;
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
        // 실제 구현에서는 데이터베이스에 저장하는 로직이 들어갈 것입니다.
        /** @var User $user */ 
        $user = User::init()
            ->setId($userData->id)
            ->setEmail($userData->email)
            ->setPassword($userData->password)
            ->build(); 
        return $this->userRepository->save($user);
    }
    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?User {
        return $this->userRepository->findOneById((string)$id);
    }
}
