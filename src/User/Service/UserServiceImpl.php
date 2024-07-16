<?php

namespace Src\User\Service;

use Src\common\Attributes\Service;
use Src\User\Controller\UserControllerImpl;

#[Service(UserService::class, UserServiceImpl::class)]
class UserServiceImpl implements UserService
{

}