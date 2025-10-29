<?php

declare(strict_types=1);

namespace App\Chatbot\Service;

use function count;

use App\Chatbot\Contract\ChatbotPluginInterface;
use App\Chatbot\Logger\Attribute\LoggerChannel;
use App\Chatbot\Logger\Contracts\ChannelAwareLoggerInterface;
use App\Entity\Message;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Throwable;

final class ChatbotPluginManager implements ChannelAwareLoggerInterface
{
    private array $sortedChatbotPlugins = [];

    public function __construct(
        #[TaggedIterator(tag: 'app.chatbot.plugin')]
        iterable $chatbotPlugins,
        #[LoggerChannel(name: 'manager')]
        private readonly LoggerInterface $logger,
    ) {
        $this->sortedChatbotPlugins = $this->sortChatbotPluginsByPriority($chatbotPlugins);
        $this->logRegisteredPlugins();
    }

    public function processMessage(Message $message): void
    {
        $processedCount = 0;

        foreach ($this->sortedChatbotPlugins as $plugin) {
            if (!$plugin->supports($message)) {
                continue;
            }

            try {
                $this->logger->debug('Processing message with plugin', [
                    'plugin' => $plugin->getName(),
                    'message_id' => $message->getId(),
                    'priority' => $plugin->getPriority(),
                ]);

                $plugin->process($message);
                ++$processedCount;
            } catch (Throwable $e) {
                $this->logger->error('Plugin execution failed', [
                    'plugin' => $plugin->getName(),
                    'message_id' => $message->getId(),
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        if (0 === $processedCount) {
            $this->logger->warning('No plugins processed the message', [
                'message_id' => $message->getId(),
            ]);
        }
    }

    /**
     * @param iterable<ChatbotPluginInterface> $plugins
     *
     * @return array<ChatbotPluginInterface>
     */
    private function sortChatbotPluginsByPriority(iterable $plugins): array
    {
        $pluginArray = iterator_to_array($plugins);

        usort(
            $pluginArray,
            static fn (ChatbotPluginInterface $a, ChatbotPluginInterface $b): int => $b->getPriority() <=> $a->getPriority()
        );

        return $pluginArray;
    }

    private function logRegisteredPlugins(): void
    {
        $pluginInfo = array_map(
            static fn (ChatbotPluginInterface $plugin): array => [
                'name' => $plugin->getName(),
                'priority' => $plugin->getPriority(),
            ],
            $this->sortedChatbotPlugins
        );

        $this->logger->info('Chatbot plugins registered', [
            'count' => count($this->sortedChatbotPlugins),
            'plugins' => $pluginInfo,
        ]);
    }
}
