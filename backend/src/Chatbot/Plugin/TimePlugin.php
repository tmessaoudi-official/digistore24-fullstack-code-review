<?php

declare(strict_types=1);

namespace App\Chatbot\Plugin;

use App\Entity\Message;
use DateTimeImmutable;

final class TimePlugin extends AbstractChatbotPlugin
{
    private const array TIME_KEYWORDS = [
        'time',
        'what time',
        'current time',
        'clock',
    ];

    private const array DATE_KEYWORDS = [
        'date',
        'what date',
        'today',
        'current date',
    ];

    private const int PRIORITY = 20;

    public function process(Message $message): void
    {
        $content = $this->normalizeContent($message->getContent());

        if ($this->containsKeyword($content, self::TIME_KEYWORDS)) {
            $time = new DateTimeImmutable()->format('H:i:s');
            $this->createBotResponse($message, "The current time is {$time}.");

            return;
        }

        if ($this->containsKeyword($content, self::DATE_KEYWORDS)) {
            $date = new DateTimeImmutable()->format('l, F j, Y');
            $this->createBotResponse($message, "Today is {$date}.");

            return;
        }
    }

    public function getName(): string
    {
        return 'date_time_chatbot';
    }

    public function supports(Message $message): bool
    {
        if (!parent::supports($message)) {
            return false;
        }

        $content = $this->normalizeContent($message->getContent());

        return $this->containsKeyword($content, [...self::TIME_KEYWORDS, ...self::DATE_KEYWORDS]);
    }

    public function getPriority(): int
    {
        return self::PRIORITY;
    }
}
