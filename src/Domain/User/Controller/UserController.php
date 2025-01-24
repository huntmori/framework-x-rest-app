<?php

namespace Damoyo\Api\Domain\User\Controller;

use Damoyo\Api\Common\Http\RequestMapper;
use Damoyo\Api\Common\Dto\ResponseDto;
use Damoyo\Api\Common\Exception\GlobalExceptionHandler;
use Damoyo\Api\Common\Routing\HttpMethod;
use Damoyo\Api\Common\Routing\Route;
use Damoyo\Api\Domain\User\Dto\UserCreate\UserCreateRequest;
use Damoyo\Api\Domain\User\Dto\UserUpdate\UserUpdateRequest;
use Damoyo\Api\Domain\User\Mapper\UserMapper;
use Damoyo\Api\Domain\User\Service\UserService;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

class UserController
{
    private UserMapper $mapper;
    private UserService $userService;
    private GlobalExceptionHandler $exceptionHandler;

    public function __construct(
        UserMapper $userMapper,
        UserService $userService, 
        GlobalExceptionHandler $exceptionHandler
    ) {
        $this->mapper = $userMapper;
        $this->userService = $userService;
        $this->exceptionHandler = $exceptionHandler;
    }

    #[Route(
        path: '/api/user',
        method: HttpMethod::GET,
        responseType: 'application/json'
    )]
    public function listUsers(ServerRequestInterface $request): ResponseDto 
    {    
        $users = $this->userService->listUsers();
        return ResponseDto::init()
            ->code(200)
            ->result(true)
            ->message('사용자 목록 조회 성공')
            ->data($users);
    }

    #[Route(
        path: '/api/user',
        method: HttpMethod::POST,
        responseType: 'application/json'
    )]
    public function createUser(
        #[RequestMapper(UserMapper::class, 'toUserCreateRequest')]
        UserCreateRequest $userRequest
    ): ResponseDto
    {
        $user = $this->userService->createUser($userRequest);
        return ResponseDto::init()
            ->code(201)
            ->result(true)
            ->message('사용자 생성 성공')
            ->data($user);
    }

    #[Route(
        path: '/api/user/{uid}',
        method: HttpMethod::GET,
        responseType: 'application/json')]
    public function getUser(ServerRequestInterface $request): ResponseDto
    {
        $uid = $request->getAttribute('uid');
        if(empty($uid)) {
            throw new Exception('UID is empty');
        }

        $user = $this->userService->getUserByUid($uid);


        return ResponseDto::init()
            ->code(200)
            ->result(true)
            ->message('사용자 조회 성공')
            ->data($user);
    }

    #[Route(
        path: '/api/user/{uid}',
        method: HttpMethod::PATCH,
        responseType: 'application/json'
    )]
    public function updateUser(
        #[RequestMapper(UserMapper::class, 'toUserUpdateRequest')]
        UserUpdateRequest $request
    ): ResponseDto
    {
        $user = $this->userService->updateUser($request->uid, $request);
        return ResponseDto::init()
            ->code(200)
            ->result(true)
            ->message('사용자 정보 수정 성공')
            ->data($user);
    }
}