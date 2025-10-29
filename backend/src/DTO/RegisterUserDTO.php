<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

readonly class RegisterUserDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Email is required')]
        #[Assert\Email(message: 'The email "{{ value }}" is not a valid email address')]
        public string $email,

        #[Assert\NotBlank(message: 'Password is required')]
        #[Assert\Length(
            min: 8,
            minMessage: 'Password must be at least {{ limit }} characters long'
        )]
        #[Assert\Regex(
            pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            message: 'Password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character'
        )]
        public string $password,

        #[Assert\NotBlank(message: 'Name is required')]
        #[Assert\Length(
            min: 2,
            max: 255,
            minMessage: 'Name must be at least {{ limit }} characters long',
            maxMessage: 'Name cannot be longer than {{ limit }} characters'
        )]
        public string $name,
    ) {
    }
}
