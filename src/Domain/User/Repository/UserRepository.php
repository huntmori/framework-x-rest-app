<?php declare(strict_types=1);

namespace Damoyo\Api\Domain\User\Repository;

use Damoyo\Api\Domain\User\Entity\User;

interface UserRepository
{
    public function find(): array;
    public function findOneById(string $id): ?User;
    public function findOneByEmail(string $email): ?User;
    public function findOneByUid(string $uid): ?User;
    
    public function save(User $user): ?int;

    public function findByUid(string $uid): ?User;
}