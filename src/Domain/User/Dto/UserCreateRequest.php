<?php
namespace Damoyo\Api\Domain\User\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserCreateRequest
{
    public string $id;
    public string $email;
    public string $password;

    public function init(): self {
        return new self();
    }
}