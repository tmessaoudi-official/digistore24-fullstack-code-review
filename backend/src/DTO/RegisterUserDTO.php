<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
        public readonly string $email,

        #[Assert\NotBlank]
        #[Assert\Length(
            min: 8,
            minMessage: 'Password must be at least {{ limit }} characters long'
        )]
        #[Assert\Regex(
            pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            message: 'Password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character'
        )]
        public readonly string $password,

        #[Assert\NotBlank]
        #[Assert\Length(
            min: 2,
            max: 255,
            minMessage: 'Name must be at least {{ limit }} characters long',
            maxMessage: 'Name cannot be longer than {{ limit }} characters'
        )]
        public readonly string $name,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            name: $data['name'] ?? '',
        );
    }
}
