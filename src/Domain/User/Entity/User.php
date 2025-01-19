<?php

/**
 * Class User
 *
 * Represents a user entity with properties such as `seq`, `uid`, `id`, `email`,
 * `password`, `createdAt`, and `updatedAt`. This class provides getter and
 * setter methods for each property, along with static methods for initialization
 * and construction from a MySQL result row.
 */

namespace Damoyo\Api\Domain\User\Entity;

use /**
 * DateTime is a class in PHP that represents date and time.
 * It provides methods for manipulating and formatting date/time values.
 * This class allows for object-oriented handling of dates and times,
 * offering advanced features such as time zone management, date arithmetic,
 * and parsing or generating dates in various formats.
 *
 * Key functionalities of the DateTime class include:
 * - Creating date/time objects for the current or specific date/time.
 * - Formatting date/time into strings using specific patterns.
 * - Modifying date/time using relative and absolute changes (e.g., adding days).
 * - Handling timezone-sensitive operations.
 * - Performing comparisons or calculations between date/time objects.
 *
 * The DateTime class is part of PHP's DateTime extension
 * and integrates closely with related classes like DateTimeImmutable,
 * DateTimeZone, and DateInterval.
 */
    DateTime;

/**
 * Class User
 *
 * Represents a user entity with properties such as identifiers, email, password, and timestamps for creation and updates.
 * The class includes methods for setting and getting properties, and initializing the entity from a MySQL result row.
 */
class User
{
    // Public properties at the top
    public int $seq;
    public string $uid = '';
    public string $id = '';
    public string $email = '';
    public string $password = '';
    public DateTime $createdAt;

    // Private properties
    public DateTime $updatedAt;

    // Constructor
    public function __construct()
    {
        // Constructor implementation
    }

    // Static methods
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

    // Getter for email
    public function getEmail(): string
    {
        return $this->email;
    }

    // Setter methods moved to the bottom
    public function setSeq(int $seq): self
    {
        $this->seq = $seq;
        return $this;
    }

    public function setUid(string $uid): self
    {
        $this->uid = $uid;
        return $this;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setEmail(string $value): self
    {
        $this->email = $value;
        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}