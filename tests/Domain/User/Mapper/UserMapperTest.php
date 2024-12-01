<?php
namespace Tests\Domain\User\Mapper;

require_once '/workspace/framework-x-rest-app/vendor/autoload.php';

use Damoyo\Api\Domain\User\Dto\UserCreateRequest;
use Damoyo\Api\Domain\User\Mapper\UserMapper;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Symfony\Component\Validator\Validation;

class UserMapperTest extends TestCase
{
    private UserMapper $userMapper;
    
    protected function setUp(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
            
        $this->userMapper = new UserMapper($validator);
    }

    public function testValidUserRequest(): void
    {
        // Given
        $requestBody = [
            'id' => 'testuser123',
            'email' => 'test@example.com',
            'password' => 'Test123!@#'
        ];
        
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')
            ->willReturn($requestBody);

        // When
        $userRequest = $this->userMapper->userCreateRequest($request);

        // Then
        $this->assertInstanceOf(UserCreateRequest::class, $userRequest);
        $this->assertEquals('testuser123', $userRequest->id);
        $this->assertEquals('test@example.com', $userRequest->email);
        $this->assertEquals('Test123!@#', $userRequest->password);
    }

    /**
     * @dataProvider invalidRequestDataProvider
     */
    public function testInvalidUserRequest(array $requestBody, string $expectedErrorMessage): void
    {
        // Given
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')
            ->willReturn($requestBody);

        // Expect
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        // When
        $this->userMapper->userCreateRequest($request);
    }

    public function invalidRequestDataProvider(): array
    {
        return [
            'empty_id' => [
                [
                    'id' => '',
                    'email' => 'test@example.com',
                    'password' => 'Test123!@#'
                ],
                'ID는 필수 입력값입니다.'
            ],
            'short_id' => [
                [
                    'id' => 'ab',
                    'email' => 'test@example.com',
                    'password' => 'Test123!@#'
                ],
                'ID는 최소 3자 이상이어야 합니다.'
            ],
            'invalid_email' => [
                [
                    'id' => 'testuser123',
                    'email' => 'invalid-email',
                    'password' => 'Test123!@#'
                ],
                '유효한 이메일 주소를 입력해주세요.'
            ],
            'short_password' => [
                [
                    'id' => 'testuser123',
                    'email' => 'test@example.com',
                    'password' => 'Test1!'
                ],
                '비밀번호는 최소 8자 이상이어야 합니다.'
            ],
            'invalid_password_format' => [
                [
                    'id' => 'testuser123',
                    'email' => 'test@example.com',
                    'password' => 'password123'
                ],
                '비밀번호는 영문, 숫자, 특수문자를 포함해야 합니다.'
            ]
        ];
    }

    public function testInvalidJsonRequest(): void
    {
        // Given
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')
            ->willReturn(null);

        // Expect
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Request body must be a valid JSON');

        // When
        $this->userMapper->userCreateRequest($request);
    }
}
