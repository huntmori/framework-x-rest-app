<?php

namespace Src\User\Service;

use Src\Common\Attributes\Injection;
use Src\Common\Attributes\Service;
use Src\User\Controller\UserControllerImpl;
use Src\User\Repository\UserRepository;

#[Service(UserService::class, UserServiceImpl::class)]
#[Injection(
    UserService::class,
    UserServiceImpl::class,
    [ UserRepository::class ]
)]
class UserServiceImpl implements UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
}