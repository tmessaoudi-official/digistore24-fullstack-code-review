<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreateMessageDTO;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;

final class MessageService
{
    public function __construct(
        private readonly MessageRepository $messageRepository,
    ) {
    }

    public function getAllMessages(): array
    {
        return $this->messageRepository->findAllOrderedById();
    }

    public function getUserMessages(User $user): array
    {
        return $this->messageRepository->findByUser($user);
    }

    public function createMessage(CreateMessageDTO $dto, User $user): Message
    {
        $message = new Message();
        $message->setContent($dto->message);
        $message->setUser($user);
        $message->setStatus(Message::STATUS_SENT);

        $this->messageRepository->save($message, true);

        return $message;
    }

    public function updateMessageStatus(Message $message, string $status): Message
    {
        $message->setStatus($status);
        $this->messageRepository->save($message, true);

        return $message;
    }
}
