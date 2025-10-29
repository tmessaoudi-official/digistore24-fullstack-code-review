<?php

declare(strict_types=1);

namespace App\Chatbot\Plugin;

use App\Chatbot\Logger\Attribute\LoggerChannel;
use App\Chatbot\Logger\Contracts\ChannelAwareLoggerInterface;
use App\Entity\Message;
use App\Repository\MessageRepository;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;

final class DateTimePlugin extends AbstractChatbotPlugin implements ChannelAwareLoggerInterface
{
    public function __construct(
        protected readonly MessageRepository $messageRepository,
        #[LoggerChannel(name: 'date_time')]
        protected readonly LoggerInterface $logger,
    ) {
    }
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
