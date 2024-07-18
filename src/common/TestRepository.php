<?php

namespace Src\common;

use React\MySQL\Io\LazyConnection;
<<<<<<< HEAD
use Src\common\Attributes\Repository;

#[Repository(TestRepository::class, TestRepository::class)]
=======

>>>>>>> origin/main
class TestRepository extends BaseRepository
{
    public function __construct(LazyConnection $connection)
    {
        parent::__construct($connection);
    }


}