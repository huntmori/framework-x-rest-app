<?php

namespace Src\User\Repository;

use React\MySQL\Io\LazyConnection;
use Src\common\Attributes\Repository;
use Src\common\BaseRepository;

#[Repository(UserRepository::class, UserRepositoryImpl::class)]
class UserRepositoryImpl extends BaseRepository implements UserRepository
{
    public function __construct(LazyConnection $connection)
    {
        parent::__construct($connection);
    }


}