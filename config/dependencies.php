<?php

use Damoyo\Api\Common\Database\DatabaseService;
use Damoyo\Api\Common\Exception\GlobalExceptionHandler;
use Damoyo\Api\Domain\User\Controller\UserController;
use Damoyo\Api\Domain\User\Mapper\UserMapper;
use Damoyo\Api\Domain\User\Repository\UserRepository;
use Damoyo\Api\Domain\User\Repository\UserRepositoryImpl;
use Damoyo\Api\Domain\User\Service\UserService;
use Damoyo\Api\Domain\User\Service\UserServiceImpl;
use function DI\autowire;
use function DI\get;

return [
    GlobalExceptionHandler::class => autowire(),
    
    UserRepository::class => autowire(UserRepositoryImpl::class)
        ->constructorParameter('database', get(DatabaseService::class)),
    
    UserService::class => autowire(UserServiceImpl::class)
        ->constructorParameter('repository', get(UserRepository::class)),
    
    UserMapper::class => autowire(),
    UserController::class => autowire()
        ->constructorParameter('userMapper', get(UserMapper::class))
        ->constructorParameter('userService', get(UserService::class))
        ->constructorParameter('exceptionHandler', get(GlobalExceptionHandler::class)),
];
