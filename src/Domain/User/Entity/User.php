<?php

namespace Damoyo\Api\Domain\User\Entity;

use DateTime;

class User
{
    
    private int $_seq = 0;
    public int $seq {
        get => $this->_seq;
        set(int $value) {
            $this->_seq = $value;
        }
    }
    public function setSeq(int $seq): self
    {
        $this->_seq = $seq;
        return $this;
    }

    private string $_uid = '';
    public string $uid {
        get => $this->_uid;
        set(string $value) {
            $this->_uid = $value;
        }
    }
    public function setUid(string $uid): self
    {
        $this->_uid = $uid;
        return $this;
    }

    private string $_id = '';
    public string $id {
        get => $this->_id;
        set(string $value) {
            $this->_id = $value;
        }
    }
    public function setId(string $id): self
    {
        $this->_id = $id;
        return $this;
    }

    private string $_email = '';
    public string $email {
        get => $this->_email;
        set(string $value) {
            $this->_email = $value;
        }
    }
    public function setEmail(string $email): self
    {
        $this->_email = $email;
        return $this;
    }

    private string $_password = '';
    public string $password {
        get => $this->_password;
        set(string $value) {
            $this->_password = $value;
        }
    }
    public function setPassword(string $password): self
    {
        $this->_password = $password;
        return $this;
    }

    private DateTime $_createdAt;
    public DateTime $createdAt {
        get => $this->_createdAt;
        set(DateTime $value) {
            $this->_createdAt = $value;
        }
    }
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->_createdAt = $createdAt;
        return $this;
    }

    private DateTime $_updatedAt;
    public DateTime $updatedAt {
        get => $this->_updatedAt;
        set(DateTime $value) {
            $this->_updatedAt = $value;
        }
    }
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->_updatedAt = $updatedAt;
        return $this;
    }


    public function __construct()
    {
        $this->_createdAt = new DateTime();
        $this->_updatedAt = new DateTime();
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