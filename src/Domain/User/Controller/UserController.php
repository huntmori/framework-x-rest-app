<?php

namespace Damoyo\Api\Domain\User\Controller;

use Damoyo\Api\Common\Dto\ResponseDto;
use Damoyo\Api\Common\Routing\Route;
use Damoyo\Api\Common\Routing\HttpMethod;
use Damoyo\Api\Domain\User\Mapper\UserMapper;
use Damoyo\Api\Domain\User\Service\UserService;
use Damoyo\Api\Common\Exception\GlobalExceptionHandler;
use Damoyo\Api\Common\Exception\ValidationException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Exception;

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

    #[Route(method: HttpMethod::GET, path: '/api/user')]
    public function listUsers(ServerRequestInterface $request): ResponseDto 
    {    
        $users = $this->userService->listUsers();
        return ResponseDto::init()
            ->code(200)
            ->result(true)
            ->message('사용자 목록 조회 성공')
            ->data($users);
    }

    #[Route(method: HttpMethod::POST, path: '/api/user')]
    public function creatUser(ServerRequestInterface $request): ResponseDto
    {
        $userRequest = $this->mapper->toUserCreateRequest($request);
        $user = $this->userService->createUser($userRequest);
        return ResponseDto::init()
            ->code(201)
            ->result(true)
            ->message('사용자 생성 성공')
            ->data($user);
    }

    #[Route(method: HttpMethod::GET, path: '/api/user/{uid}')]
    public function getUser(ServerRequestInterface $request): ResponseDto
    {
        $uid = $request->getAttribute('uid');
        if(empty($uid)) {
            throw new Exception('UID is empty');
        }

        $user = null;
        if (!empty($uid)) {
            $user = $this->userService->getUserByUid($uid);
        }


        return ResponseDto::init()
            ->code(200)
            ->result(true)
            ->message('사용자 조회 성공')
            ->data($user);
    }

    #[Route(method:HttpMethod::PATCH, path: '/api/user/{uid}')]
    public function updateUser(ServerRequestInterface $request): ResponseDto
    {
        $uid = $request->getAttribute('uid');

        if (empty($uid)) {
            throw new Exception("UID is empty");
        }

        $userRequest = $this->mapper->toUserUpdateRequest($request);
        $user = $this->userService->updateUser($uid, $userRequest);
        return ResponseDto::init()
            ->code(200)
            ->result(true)
            ->message('사용자 정보 수정 성공')
            ->data($user);
    }
}