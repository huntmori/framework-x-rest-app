<?php

use Damoyo\Api\Domain\User\Repository\UserRepository;
use Damoyo\Api\Domain\User\Repository\UserRepositoryImpl;
use Damoyo\Api\Domain\User\Service\UserService;
use Damoyo\Api\Domain\User\Service\UserServiceImpl;
use Damoyo\Api\Common\Database\DatabaseService;

return [
    UserRepository::class => DI\create(UserRepositoryImpl::class)
        ->constructor(DI\get(DatabaseService::class)),
    
    UserService::class => DI\create(UserServiceImpl::class)
        ->constructor(DI\get(UserRepository::class)),
];
