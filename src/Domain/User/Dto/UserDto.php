<?php
namespace Damoyo\Api\Domain\User\Dto;

class UserDto
{
    public string $uid;
    public string $id;
    public string $email;
    public string $created_at;
    public string $updated_at;
    public array $profiles = [];
}