<?php
namespace Damoyo\Api\Domain\User\Dto\UserCreate;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserCreateRequest
{
    public ServerRequestInterface $request;
    #[Assert\NotBlank(message: 'ID는 필수 입력값입니다.')]
    public string $id;

    #[Assert\NotBlank(message: '이메일은 필수 입력값입니다.')]
    #[Assert\Email(message: '유효한 이메일 주소를 입력해주세요.')]
    public string $email;

    #[Assert\NotBlank(message: '비밀번호는 필수 입력값입니다.')]
    #[Assert\Length(
        min: 6,
        max: 50,
        minMessage: '비밀번호는 최소 {{ limit }}자 이상이어야 합니다.',
        maxMessage: '비밀번호는 최대 {{ limit }}자를 초과할 수 없습니다.'
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]/',
        message: '비밀번호는 영문자, 숫자, 특수문자를 모두 포함해야 합니다.'
    )]
    public string $password;

    public static function init(): self {
        return new self();
    }

    public function id(string $id): self 
    {
        $this->id = $id;
        return $this;
    }
}