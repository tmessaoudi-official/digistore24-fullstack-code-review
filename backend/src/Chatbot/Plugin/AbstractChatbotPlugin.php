<?php

declare(strict_types=1);

namespace App\Chatbot\Plugin;

use App\Chatbot\Contract\ChatbotPluginInterface;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;

abstract class AbstractChatbotPlugin implements ChatbotPluginInterface
{
    private ?User $botUser = null;

    public function __construct(
        protected readonly MessageRepository $messageRepository,
        protected readonly UserRepository $userRepository,
        protected readonly LoggerInterface $logger,
    ) {
    }

    protected function createBotResponse(Message $originalMessage, string $responseContent): Message
    {
        $botMessage = new Message();
        $botMessage->setContent($responseContent);
        $botMessage->setUser($this->getBotUser());
        $botMessage->setStatus(Message::STATUS_RECEIVED);

        $this->messageRepository->save($botMessage, true);

        $this->logger->info('Chatbot generated response', [
            'plugin' => $this->getName(),
            'bot_user_id' => $this->getBotUser()->getId(),
            'original_message_id' => $originalMessage->getId(),
            'response_message_id' => $botMessage->getId(),
            'original_content' => $originalMessage->getContent(),
            'response_content' => $responseContent,
        ]);

        return $botMessage;
    }

    protected function getBotUser(): User
    {
        if ($this->botUser === null) {
            $botEmail = sprintf('bot+%s@local.io', $this->getName());
            $botUser = $this->userRepository->findByEmail($botEmail);

            if ($botUser === null) {
                $botUser = new User();
                $botUser->setEmail($botEmail);
                $botUser->setName($this->getBotDisplayName());
                $botUser->setPassword(''); // Bot users don't need passwords
                $botUser->setRoles(['ROLE_BOT']);

                $this->userRepository->save($botUser, true);

                $this->logger->info('Created new bot user', [
                    'plugin' => $this->getName(),
                    'bot_email' => $botEmail,
                    'bot_name' => $this->getBotDisplayName(),
                ]);
            }

            $this->botUser = $botUser;
        }

        return $this->botUser;
    }

    protected function getBotDisplayName(): string
    {
        return ucwords(str_replace('_', ' ', $this->getName()));
    }

    protected function normalizeContent(string $content): string
    {
        return mb_strtolower(trim($content));
    }

    protected function containsKeyword(string $content, array $keywords): bool
    {
        $normalized = $this->normalizeContent($content);

        foreach ($keywords as $keyword) {
            if (str_contains($normalized, mb_strtolower($keyword))) {
                return true;
            }
        }

        return false;
    }

    public function supports(Message $message): bool
    {
        return null !== $message->getUser();
    }
}
