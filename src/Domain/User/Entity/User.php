<?php

namespace Damoyo\Api\Domain\User\Entity;

use DateTime;

class User
{
    public int $seq;
    public function setSeq(int $seq): self
    {
        $this->seq = $seq;
        return $this;
    }

    public string $uid = '';
    public function setUid(string $uid): self
    {
        $this->uid = $uid;
        return $this;
    }

    public string $id = '';
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public string $email = '';
    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $value): self
    {
        $this->email = $value;
        return $this;
    }

    public string $password = '';
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public DateTime $createdAt;
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    private DateTime $updatedAt;
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function __construct()
    {
        // Constructor implementation
    }

    public static function init(): self
    {
        return new self();
    }

    public function build(): self
    {
        return $this;
    }

    public static function fromMysqlResultRow(array $userData): self
    {
        return User::init()
            ->setSeq($userData['seq'])
            ->setId($userData['id'])
            ->setUid($userData['uid'])
            ->setEmail($userData['email'])
            ->setPassword($userData['password'])
            ->setCreatedAt(new DateTime($userData['created_at']))
            ->setUpdatedAt(new DateTime($userData['updated_at']))
            ->build();
    }
}