<?php

declare(strict_types=1);

namespace App\Chatbot\Plugin;

use App\Chatbot\Contract\ChatbotPluginInterface;
use App\Entity\Message;
use App\Repository\MessageRepository;
use Psr\Log\LoggerInterface;

abstract class AbstractChatbotPlugin implements ChatbotPluginInterface
{
    public function __construct(
        protected readonly MessageRepository $messageRepository,
        protected readonly LoggerInterface $logger,
    ) {
    }

    protected function createBotResponse(Message $originalMessage, string $responseContent): Message
    {
        $botMessage = new Message();
        $botMessage->setContent($responseContent);
        $botMessage->setUser($originalMessage->getUser());
        $botMessage->setStatus(Message::STATUS_RECEIVED);

        $this->messageRepository->save($botMessage, true);

        $this->logger->info('Chatbot generated response', [
            'plugin' => $this->getName(),
            'original_message_id' => $originalMessage->getId(),
            'response_message_id' => $botMessage->getId(),
            'original_content' => $originalMessage->getContent(),
            'response_content' => $responseContent,
        ]);

        return $botMessage;
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
