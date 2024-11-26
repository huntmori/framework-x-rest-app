<?php

namespace Damoyo\Api\Domain\User\Controller;

use Damoyo\Api\Common\Dto\ResponseDto;
use Damoyo\Api\Common\Routing\Route;
use Damoyo\Api\Common\Routing\HttpMethod;
use Damoyo\Api\Domain\User\Service\UserService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class UserController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/user', method: HttpMethod::GET)]
    public function listUsers(ServerRequestInterface $request): ResponseDto {
        $response = new ResponseDto();
        $response->code = 200;
        $response->result = true;
        $response->message = '사용자 목록 조회 성공';
        $response->data = $this->userService->listUsers();
        return $response;
    }

    #[Route('/user/create', method: HttpMethod::POST)]
    public function createUser(ServerRequestInterface $request): ResponseDto {
        $response = new ResponseDto();
        $requestData = $request->getParsedBody();
        
        $response->code = 201;
        $response->result = true;
        $response->message = '사용자 생성 성공';
        $response->data = $this->userService->createUser($requestData);
        return $response;
    }

    public function getUsersByName(string $name) {
        
    }
}