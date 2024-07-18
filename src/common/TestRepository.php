<?php

namespace Src\common;

use React\MySQL\Io\LazyConnection;
use Src\common\Attributes\Repository;

#[Repository(TestRepository::class, TestRepository::class)]
class TestRepository extends BaseRepository
{
    public function __construct(LazyConnection $connection)
    {
        parent::__construct($connection);
    }
}