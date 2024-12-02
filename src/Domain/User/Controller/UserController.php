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
        try {
            $users = $this->userService->listUsers();
            return ResponseDto::init()
                ->code(200)
                ->result(true)
                ->message('사용자 목록 조회 성공')
                ->data($users);
        } catch (Throwable $e) {
            return $this->exceptionHandler->handle($e);
        }
    }

    #[Route(method: HttpMethod::POST, path: '/api/user')]
    public function creatUser(ServerRequestInterface $request): ResponseDto
    {
        try {
            $userRequest = $this->mapper->toUserCreateRequest(json_decode($request->getBody()->getContents(), true));
            $user = $this->userService->createUser($userRequest);
            return ResponseDto::init()
                ->code(201)
                ->result(true)
                ->message('사용자 생성 성공')
                ->data($user);
        } catch (Throwable $e) {
            return $this->exceptionHandler->handle($e);
        }
    }

    #[Route(method: HttpMethod::GET, path: '/api/user/{uid}')]
    public function getUser(ServerRequestInterface $request): ResponseDto
    {
        try {
            $uid = $request->getAttribute('uid');

            $user = null;
            if (!empty($uid)) {
                $user = $this->userService->getUserByUid($uid);
            }


            return ResponseDto::init()
                ->code(200)
                ->result(true)
                ->message('사용자 조회 성공')
                ->data($user);
        } catch (Throwable $e) {
            return $this->exceptionHandler->handle($e);
        }
    }

    #[Route(method:HttpMethod::PATCH, path: '/api/user/{uid}')]
    public function updateUser(ServerRequestInterface $request): ResponseDto
    {
        try {
            $uid = $request->getAttribute('uid');
            $userRequest = $this->mapper->toUserUpdateRequest(json_decode($request->getBody()->getContents(), true));
            $user = $this->userService->updateUser($uid, $userRequest);
            return ResponseDto::init()
                ->code(200)
                ->result(true)
                ->message('사용자 정보 수정 성공')
                ->data($user);
        } catch (Throwable $e) {
            return $this->exceptionHandler->handle($e);
        }
    }
}