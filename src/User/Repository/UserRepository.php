<?php

namespace Src\User\Repository;

use React\MySQL\ConnectionInterface;
use Src\Common\BaseRepository;

interface UserRepository
{
    public function createUser($param);
    public function updateUser($param);
    public function deleteUser($param);
    public function getUser($param);
    public function getUserList($param);
}