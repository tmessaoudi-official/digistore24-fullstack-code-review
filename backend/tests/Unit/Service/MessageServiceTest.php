<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Chatbot\Service\ChatbotPluginManager;
use App\DTO\CreateMessageDTO;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Service\MessageService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * @internal
 *
 * @coversNothing
 */
final class MessageServiceTest extends TestCase
{
    private MessageRepository&MockObject $messageRepository;
    private ChatbotPluginManager $pluginManager;
    private MessageService $messageService;
    private User $testUser;

    protected function setUp(): void
    {
        $this->messageRepository = $this->createMock(MessageRepository::class);
        $this->pluginManager = new ChatbotPluginManager([], new NullLogger());

        $this->messageService = new MessageService(
            $this->messageRepository,
            $this->pluginManager
        );

        $this->testUser = new User();
        $this->testUser->setEmail('test@example.com');
        $this->testUser->setName('Test User');
    }

    public function testGetAllMessages(): void
    {
        $expectedMessages = [
            $this->createMessage('Message 1'),
            $this->createMessage('Message 2'),
        ];

        $this->messageRepository
            ->expects(self::once())
            ->method('findAllOrderedById')
            ->willReturn($expectedMessages)
        ;

        $result = $this->messageService->getAllMessages();

        self::assertSame($expectedMessages, $result);
    }

    public function testGetUserMessages(): void
    {
        $expectedMessages = [
            $this->createMessage('User Message 1'),
            $this->createMessage('User Message 2'),
        ];

        $this->messageRepository
            ->expects(self::once())
            ->method('findByUser')
            ->with($this->testUser)
            ->willReturn($expectedMessages)
        ;

        $result = $this->messageService->getUserMessages($this->testUser);

        self::assertSame($expectedMessages, $result);
    }

    public function testCreateMessage(): void
    {
        $dto = new CreateMessageDTO('Test message content');

        $this->messageRepository
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(fn (Message $message) => $message->getContent() === $dto->message
                        && $message->getUser() === $this->testUser
                        && Message::STATUS_SENT === $message->getStatus()),
                true
            )
        ;

        $result = $this->messageService->createMessage($dto, $this->testUser);

        self::assertInstanceOf(Message::class, $result);
        self::assertSame($dto->message, $result->getContent());
        self::assertSame($this->testUser, $result->getUser());
        self::assertSame(Message::STATUS_SENT, $result->getStatus());
    }

    public function testUpdateMessageStatus(): void
    {
        $message = $this->createMessage('Test message');
        $newStatus = Message::STATUS_RECEIVED;

        $this->messageRepository
            ->expects(self::once())
            ->method('save')
            ->with($message, true)
        ;

        $result = $this->messageService->updateMessageStatus($message, $newStatus);

        self::assertSame($message, $result);
        self::assertSame($newStatus, $message->getStatus());
    }

    public function testCreateMessageSavesWithFlush(): void
    {
        $dto = new CreateMessageDTO('Test');

        $this->messageRepository
            ->expects(self::once())
            ->method('save')
            ->with(self::isInstanceOf(Message::class), true)
        ;

        $this->messageService->createMessage($dto, $this->testUser);
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
