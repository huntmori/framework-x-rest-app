<?php

namespace Src\User\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use React\Http\Message\Response;
use Src\Common\Attributes\Controller;
use Src\Common\Attributes\Injection;
use Src\Common\Attributes\Route;
use Src\User\Service\UserService;
use Src\User\UserRequestBinder;
use Override;

#[Controller(UserController::class, UserControllerImpl::class)]
#[Injection(
    UserController::class,
    UserControllerImpl::class,
    [
        UserService::class
    ]
)]
class UserControllerImpl implements UserController
{
    private UserService $userService;
    private UserRequestBinder $requestBinder;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->requestBinder = new UserRequestBinder();
    }

    #[Override]
    #[Route(Route::PUT, "/users")]
    public function create(Request $request): Response
    {
        $result = [ 'name'=>'put:user' ];

        return Response::json($result);
    }

    #[Override]
    #[Route(Route::PATCH, "/users/{id}")]
    public function update(Request $request): Response
    {
        $result = [ 'name'=>'patch:user' ];

        return Response::json($result);
    }

    #[Override]
    #[Route(Route::DELETE, "/users/{id}")]
    public function delete(Request $request): Response
    {
        $result = [ 'name'=>'delete:user' ];

        return Response::json($result);
    }

    #[Override]
    #[Route(Route::GET, "/users/{id}")]
    public function getOne(Request $request): Response
    {
        $id = $request->getAttribute("id");
        $result = [ 'name'=>'getOne:user_'.$id ];

        return Response::json($result);
    }

    #[Override]
    #[Route(Route::GET, "/users")]
    public function getList(Request $request): Response
    {
        $result = [ 'name'=>'getList:user' ];

        return Response::json($result);
    }
}