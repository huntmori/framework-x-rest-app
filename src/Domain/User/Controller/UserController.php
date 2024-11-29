<?php

namespace Damoyo\Api\Domain\User\Controller;

use Damoyo\Api\Common\Dto\ResponseDto;
use Damoyo\Api\Common\Routing\Route;
use Damoyo\Api\Common\Routing\HttpMethod;
use Damoyo\Api\Domain\User\Service\UserService;
use Damoyo\Api\Common\Exception\GlobalExceptionHandler;
use Damoyo\Api\Common\Exception\ValidationException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class UserController
{
    private UserService $userService;
    private GlobalExceptionHandler $exceptionHandler;

    public function __construct(UserService $userService, GlobalExceptionHandler $exceptionHandler)
    {
        $this->userService = $userService;
        $this->exceptionHandler = $exceptionHandler;
    }

    #[Route('/user', method: HttpMethod::GET)]
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

    #[Route('/user/create', method: HttpMethod::POST)]
    public function createUser(ServerRequestInterface $request): ResponseDto 
    {
        try {
            $requestData = $request->getParsedBody();
            
            // 필수 데이터 검증
            if (empty($requestData['name']) || empty($requestData['email'])) {
                throw new ValidationException('이름과 이메일은 필수 입력 항목입니다.');
            }
            
            $user = $this->userService->createUser($requestData);
            return ResponseDto::init()
                ->code(201)
                ->result(true)
                ->message('사용자 생성 성공')
                ->data($user);
        } catch (Throwable $e) {
            return $this->exceptionHandler->handle($e);
        }
    }

    #[Route('/user/{name}', method: HttpMethod::GET)]
    public function getUsersByName(ServerRequestInterface $request, array $args): ResponseDto 
    {
        try {
            $name = $args['name'] ?? '';
            if (empty($name)) {
                throw new ValidationException('사용자 이름은 필수입니다.');
            }
            
            $users = $this->userService->getUsersByName($name);
            return ResponseDto::init()
                ->code(200)
                ->result(true)
                ->message('사용자 조회 성공')
                ->data($users);
        } catch (Throwable $e) {
            return $this->exceptionHandler->handle($e);
        }
    }
}