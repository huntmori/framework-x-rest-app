<?php

namespace Damoyo\Api\Domain\User\Mapper;

use Damoyo\Api\Domain\User\Dto\UserCreate\UserCreateRequest;
use Damoyo\Api\Domain\User\Dto\UserUpdate\UserUpdateRequest;
use Damoyo\Api\Domain\User\Entity\User;
use DateTime;
use DateTimeZone;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use InvalidArgumentException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserMapper
{
    private ValidatorInterface $validator;

    public function __construct()
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function requestBodyToAssociativeArray(ServerRequestInterface $request) : array 
    {
        return json_decode($request->getBody()->getContents(), true);
    }

    public function toUserCreateRequest(ServerRequestInterface $request): UserCreateRequest
    {
        $data = $this->requestBodyToAssociativeArray($request);

        $request = new UserCreateRequest();
        $request->request = $request;
        $request->id = $data['id'] ?? null;
        $request->email = $data['email'] ?? null;
        $request->password = $data['password'] ?? null;
        
        $errors = $this->validator->validate($request);
        if (count($errors) > 0) {
            throw new InvalidArgumentException($this->formatValidationErrors($errors));
        }
        
        return $request;
    }

    public function toUserUpdateRequest(ServerRequestInterface $serverRequest): UserUpdateRequest
    {
        $data = $this->requestBodyToAssociativeArray($serverRequest);
        $request = new UserUpdateRequest();
        $request->uid = $serverRequest->getAttribute('uid');
        $request->email = $data['email'] ?? null;
        $request->name = $data['name'] ?? null;
        $request->password = $data['password'] ?? null;

        // 유효성 검사
        $violations = $this->validator->validate($request);
        if (count($violations) > 0) {
            $errorMessages = [];
            foreach ($violations as $violation) {
                $errorMessages[] = $violation->getMessage();
            }
            throw new InvalidArgumentException(implode(', ', $errorMessages));
        }

        return $request;
    }

    public function dbRowToUser(array $data) : ?User
    {
        $timezone = new DateTimeZone('Asia/Seoul');
        return User::init()
            ->setSeq($data['seq'])
            ->setId($data['id'])
            ->setUid($data['uid'])
            ->setEmail($data['email'])
            ->setPassword($data['password'])
            ->setCreatedAt(new DateTime($data['created_at'], $timezone))
            ->setUpdatedAt(new DateTime($data['updated_at'], $timezone))
            ->build();
    }

    private function formatValidationErrors(ConstraintViolationListInterface $errors): string 
    {
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getMessage();
        }
        return implode("\n", array_unique($messages));
    }
}