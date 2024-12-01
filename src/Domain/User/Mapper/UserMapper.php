<?php

namespace Damoyo\Api\Domain\User\Mapper;

use Damoyo\Api\Domain\User\Dto\UserCreateRequest;
use Damoyo\Api\Domain\User\Entity\User;
use DateTime;
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

    public function toUserCreateRequest(array $data): UserCreateRequest
    {
        $request = new UserCreateRequest();
        $request->id = $data['id'] ?? null;
        $request->email = $data['email'] ?? null;
        $request->password = $data['password'] ?? null;
        
        $errors = $this->validator->validate($request);
        if (count($errors) > 0) {
            throw new InvalidArgumentException($this->formatValidationErrors($errors));
        }
        
        return $request;
    }

    public function dbRowToUser(array $data) : ?User
    {
        return User::init()
            ->setSeq($data['seq'])
            ->setId($data['id'])
            ->setUid($data['uid'])
            ->setEmail($data['email'])
            ->setPassword($data['password'])
            ->setCreatedAt(new DateTime($data['created_at']))
            ->setUpdatedAt(new DateTime($data['updated_at']))
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