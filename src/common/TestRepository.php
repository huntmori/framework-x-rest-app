<?php

namespace Src\common;

use React\MySQL\Io\LazyConnection;

class TestRepository extends BaseRepository
{
    public function __construct(LazyConnection $connection)
    {
        parent::__construct($connection);
    }


}