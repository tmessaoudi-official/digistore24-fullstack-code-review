<?php

declare(strict_types=1);

namespace App\Chatbot\Plugin;

use App\Entity\Message;

final class GenericChatbotPlugin extends AbstractChatbotPlugin
{
    private const array KEYWORD_RESPONSES = [
        'hello' => 'Hi there! How can I help you today?',
        'hi' => 'Hello! What can I do for you?',
        'help' => 'I can assist you with various tasks. What do you need?',
        'bye' => 'Goodbye! Have a great day!',
        'thanks' => 'You\'re welcome! Happy to help!',
        'thank you' => 'You\'re welcome! Is there anything else I can help with?',
    ];

    private const int PRIORITY = 10;

    public function process(Message $message): void
    {
        $content = $this->normalizeContent($message->getContent());

        foreach (self::KEYWORD_RESPONSES as $keyword => $response) {
            if (str_contains($content, $keyword)) {
                $this->createBotResponse($message, $response);

                return;
            }
        }
    }

    public function supports(Message $message): bool
    {
        if (!parent::supports($message)) {
            return false;
        }

        $content = $this->normalizeContent($message->getContent());

        return $this->containsKeyword($content, array_keys(self::KEYWORD_RESPONSES));
    }

    public function getName(): string
    {
        return 'generic_chatbot';
    }

    public function getPriority(): int
    {
        return self::PRIORITY;
    }
}
