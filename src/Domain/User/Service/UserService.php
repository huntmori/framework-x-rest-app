<?php

namespace Damoyo\Api\Domain\User\Service;

use Damoyo\Api\Domain\User\Dto\UserCreate\UserCreateRequest;
use Damoyo\Api\Domain\User\Dto\UserUpdate\UserUpdateRequest;
use Damoyo\Api\Domain\User\Entity\User;

interface UserService
{
    public function listUsers(): array;

    public function createUser(UserCreateRequest $userData): int;

    public function findOneById(int $id): ?User;

    public function getUserByUid(string $uid): ?User;

    public function updateUser(string $uid, UserUpdateRequest $userData): ?User;
}