<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateMessageDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Message content is required')]
        #[Assert\Length(
            min: 1,
            max: 5000,
            minMessage: 'Message must be at least {{ limit }} character long',
            maxMessage: 'Message cannot be longer than {{ limit }} characters'
        )]
        public string $message,
    ) {
    }
}
