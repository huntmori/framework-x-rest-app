<?php

namespace Src\User\Controller;

use Src\common\Attributes\Controller;

#[Controller(UserController::class, UserControllerImpl::class)]
class UserControllerImpl implements UserController
{

}