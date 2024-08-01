<?php

namespace Src\User\Repository;

use React\MySQL\Io\LazyConnection;
use Src\Common\Attributes\Injection;
use Src\Common\Attributes\Repository;
use Src\Common\BaseRepository;
use Override;

#[Repository(UserRepository::class, UserRepositoryImpl::class)]
#[Injection(
    UserRepository::class,
    UserRepositoryImpl::class,
    [LazyConnection::class]
)]
class UserRepositoryImpl extends BaseRepository implements UserRepository
{
    public function __construct(LazyConnection $connection)
    {
        parent::__construct($connection);
    }


    #[Override]
    public function createUser($param)
    {
        // TODO: Implement createUser() method.
    }

    #[Override]
    public function updateUser($param)
    {
        // TODO: Implement updateUser() method.
    }

    #[Override]
    public function deleteUser($param)
    {
        // TODO: Implement deleteUser() method.
    }

    #[Override]
    public function getUser($param)
    {
        // TODO: Implement getUser() method.
    }

    #[Override]
    public function getUserList($param)
    {
        // TODO: Implement getUserList() method.
    }
}