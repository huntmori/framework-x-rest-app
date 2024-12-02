<?php

namespace Damoyo\Api\Domain\User\Service;

use Damoyo\Api\Domain\User\Dto\UserCreate\UserCreateRequest;
use Damoyo\Api\Domain\User\Repository\UserRepository;
use Damoyo\Api\Domain\User\Entity\User;
use Exception;

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
        //check ID duplication
        $idExsist = $this->userRepository->findOneById($userData->id);
        if ($idExsist !== null) {
            throw new Exception("ID duplication");
        }
        // check Email duplication
        $emailExsist = $this->userRepository->findOneByEmail($userData->email);
        if ($emailExsist !== null) {
            throw new Exception("Email duplication");
        }
        // password encryption
        $hashedPassword = password_hash($userData->password, PASSWORD_DEFAULT);
        
        /** @var User $user */ 
        $user = User::init()
            ->setId($userData->id)
            ->setEmail($userData->email)
            ->setPassword($hashedPassword)
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
