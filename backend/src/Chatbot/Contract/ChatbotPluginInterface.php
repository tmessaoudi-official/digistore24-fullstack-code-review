<?php

declare(strict_types=1);

namespace App\Chatbot\Contract;

use App\Entity\Message;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.chatbot.plugin')]
interface ChatbotPluginInterface
{
    public function process(Message $message): void;

    public function getName(): string;

    public function supports(Message $message): bool;

    public function getPriority(): int;
}
