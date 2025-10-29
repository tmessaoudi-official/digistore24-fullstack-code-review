<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateMessageDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Message cannot be empty')]
        #[Assert\Length(
            min: 1,
            max: 5000,
            minMessage: 'Message must be at least {{ limit }} characters long',
            maxMessage: 'Message cannot be longer than {{ limit }} characters'
        )]
        public readonly string $message,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            message: $data['message'] ?? '',
        );
    }
}
