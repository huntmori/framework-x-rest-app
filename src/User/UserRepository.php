<?php

namespace Src\User;

use Src\common\BaseRepository;
use React\MySQL\ConnectionInterface;
use function React\Async\await;

class UserRepository extends BaseRepository
{
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);
    }
}