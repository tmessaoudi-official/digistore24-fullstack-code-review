<?php

declare(strict_types=1);

namespace App\Tests\Unit\Chatbot\Plugin;

use App\Chatbot\Plugin\GenericChatbotPlugin;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GenericChatbotPluginTest extends TestCase
{
    private MessageRepository&MockObject $messageRepository;
    private UserRepository&MockObject $userRepository;
    private LoggerInterface&MockObject $logger;
    private GenericChatbotPlugin $plugin;
    private User $testUser;
    private User $botUser;

    protected function setUp(): void
    {
        $this->messageRepository = $this->createMock(MessageRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->testUser = new User();
        $this->testUser->setEmail('test@example.com');
        $this->testUser->setName('Test User');

        $this->botUser = new User();
        $this->botUser->setEmail('bot+generic_chatbot@local.io');
        $this->botUser->setName('Generic Chatbot');
        $this->botUser->setRoles(['ROLE_BOT']);

        $this->userRepository
            ->method('findByEmail')
            ->with('bot+generic_chatbot@local.io')
            ->willReturn($this->botUser);

        $this->plugin = new GenericChatbotPlugin(
            $this->messageRepository,
            $this->userRepository,
            $this->logger
        );
    }

    public function testGetName(): void
    {
        $this->assertEquals('generic_chatbot', $this->plugin->getName());
    }

    public function testGetPriority(): void
    {
        $this->assertEquals(10, $this->plugin->getPriority());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('keywordProvider')]
    public function testSupportsWithKeywords(string $content, bool $expected): void
    {
        $message = $this->createMessage($content);

        $this->assertEquals($expected, $this->plugin->supports($message));
    }

    public static function keywordProvider(): array
    {
        return [
            'hello keyword' => ['hello there', true],
            'hi keyword' => ['Hi everyone', true],
            'help keyword' => ['I need help', true],
            'bye keyword' => ['bye bye', true],
            'thanks keyword' => ['thanks a lot', true],
            'thank you keyword' => ['thank you very much', true],
            'no keyword' => ['random message', false],
            'uppercase HELLO' => ['HELLO', true],
            'mixed case HeLLo' => ['HeLLo', true],
        ];
    }

    public function testSupportsReturnsFalseWhenNoUser(): void
    {
        $message = new Message();
        $message->setContent('hello');

        $this->assertFalse($this->plugin->supports($message));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('responseProvider')]
    public function testProcessGeneratesCorrectResponse(string $input, string $expectedResponse): void
    {
        $message = $this->createMessage($input);

        $this->messageRepository
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function (Message $botMessage) use ($message, $expectedResponse) {
                    return $botMessage->getContent() === $expectedResponse
                        && $botMessage->getUser() === $this->botUser
                        && $botMessage->getStatus() === Message::STATUS_RECEIVED
                        && $botMessage->getInReplyTo() === $message;
                }),
                true
            );

        $this->logger->expects($this->once())->method('info');

        $this->plugin->process($message);
    }

    public static function responseProvider(): array
    {
        return [
            ['hello', 'Hi there! How can I help you today?'],
            ['hi', 'Hello! What can I do for you?'],
            ['help me', 'I can assist you with various tasks. What do you need?'],
            ['bye', 'Goodbye! Have a great day!'],
            ['thanks', 'You\'re welcome! Happy to help!'],
            ['thank you', 'You\'re welcome! Is there anything else I can help with?'],
        ];
    }

    public function testProcessStopsAtFirstMatch(): void
    {
        $message = $this->createMessage('hello and thanks');

        $this->messageRepository
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function (Message $botMessage) {
                    return $botMessage->getContent() === 'Hi there! How can I help you today?';
                }),
                true
            );

        $this->plugin->process($message);
    }

    public function testProcessSetsInReplyTo(): void
    {
        $message = $this->createMessage('hello');

        $this->messageRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (Message $botMessage) use ($message) {
                $this->assertSame($message, $botMessage->getInReplyTo());
            });

        $this->plugin->process($message);
    }

    public function testBotUserCreatedIfNotExists(): void
    {
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository
            ->method('findByEmail')
            ->with('bot+generic_chatbot@local.io')
            ->willReturn(null);

        $userRepository
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function (User $user) {
                    return $user->getEmail() === 'bot+generic_chatbot@local.io'
                        && $user->getName() === 'Generic Chatbot'
                        && in_array('ROLE_BOT', $user->getRoles());
                }),
                true
            );

        $plugin = new GenericChatbotPlugin(
            $this->messageRepository,
            $userRepository,
            $this->logger
        );

        $message = $this->createMessage('hello');
        $this->messageRepository->method('save');
        $plugin->process($message);
    }

    private function createMessage(string $content): Message
    {
        $message = new Message();
        $message->setContent($content);
        $message->setUser($this->testUser);
        $message->setStatus(Message::STATUS_SENT);

        return $message;
    }
}
