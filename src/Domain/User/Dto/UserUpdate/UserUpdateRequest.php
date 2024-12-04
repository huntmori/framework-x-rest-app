<?php

namespace Damoyo\Api\Domain\User\Dto\UserUpdate;

use Symfony\Component\Validator\Constraints as Assert;

class UserUpdateRequest
{
    #[Assert\Email(message: '유효한 이메일 형식이 아닙니다.')]
    public ?string $email = null;

    #[Assert\Length(min: 2, max: 50, minMessage: '이름은 최소 2자 이상이어야 합니다.', maxMessage: '이름은 최대 50자까지 가능합니다.')]
    public ?string $name = null;

    #[Assert\Length(min: 8, minMessage: '비밀번호는 최소 8자 이상이어야 합니다.')]
    public ?string $password = null;
}
