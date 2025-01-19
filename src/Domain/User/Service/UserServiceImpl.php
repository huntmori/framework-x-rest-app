<?php

namespace Damoyo\Api\Domain\User\Service;

use Damoyo\Api\Domain\User\Dto\UserCreate\UserCreateRequest;
use Damoyo\Api\Domain\User\Dto\UserUpdate\UserUpdateRequest;
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
        $idExist = $this->userRepository->findOneById($userData->id);
        if ($idExist !== null) {
            throw new Exception("ID duplication");
        }
        // check Email duplication
        $emailExist = $this->userRepository->findOneByEmail($userData->email);
        if ($emailExist !== null) {
            throw new Exception("Email duplication");
        }
        // password encryption
        $hashedPassword = password_hash($userData->password, PASSWORD_DEFAULT);

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
        return $this->userRepository->findByUid($uid);
    }

    public function updateUser(string $uid, UserUpdateRequest $userData): ?User
    {
        $user = $this->userRepository->findOneByUid($uid);
        
        if ($user === null) {
            throw new Exception("User not found");
        }

        // 이메일 중복 체크 (변경된 경우에만)
        if ($userData->email !== null && $userData->email !== $user->email) {
            $emailExist = $this->userRepository->findOneByEmail($userData->email);
            if ($emailExist !== null) {
                throw new Exception("Email already exists");
            }
            $user->setEmail($userData->email);
        }

        // 이름 업데이트
        if ($userData->name !== null) {
            $user->name = $userData->name;
        }

        // 비밀번호 업데이트 (변경된 경우에만)
        if ($userData->password !== null) {
            $hashedPassword = password_hash($userData->password, PASSWORD_DEFAULT);
            $user->setPassword($hashedPassword);
        }

        $this->userRepository->save($user);

        return $this->userRepository->findOneByUid($uid);
    }
}
