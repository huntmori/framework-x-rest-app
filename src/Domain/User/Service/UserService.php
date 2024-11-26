<?php

namespace Damoyo\Api\Domain\User\Service;

interface UserService
{
    public function listUsers(): array;

    public function createUser(array $userData): array;
}