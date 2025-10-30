<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Message;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 *
 * @coversNothing
 */
final class MessageTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
        $this->user->setEmail('test@example.com');
        $this->user->setName('Test User');
    }

    public function testMessageCreation(): void
    {
        $message = new Message();

        self::assertNull($message->getId());
        self::assertNull($message->getContent());
        self::assertNull($message->getUser());
        self::assertSame(Message::STATUS_SENT, $message->getStatus());
        self::assertNull($message->getInReplyTo());
    }

    public function testSetAndGetContent(): void
    {
        $message = new Message();
        $content = 'Hello, World!';

        $result = $message->setContent($content);

        self::assertSame($message, $result);
        self::assertSame($content, $message->getContent());
    }

    public function testSetAndGetUser(): void
    {
        $message = new Message();

        $result = $message->setUser($this->user);

        self::assertSame($message, $result);
        self::assertSame($this->user, $message->getUser());
    }

    public function testSetAndGetStatus(): void
    {
        $message = new Message();

        $result = $message->setStatus(Message::STATUS_RECEIVED);

        self::assertSame($message, $result);
        self::assertSame(Message::STATUS_RECEIVED, $message->getStatus());
    }

    public function testSetAndGetInReplyTo(): void
    {
        $originalMessage = new Message();
        $originalMessage->setContent('Original message');
        $originalMessage->setUser($this->user);

        $replyMessage = new Message();
        $replyMessage->setContent('Reply message');
        $replyMessage->setUser($this->user);

        $result = $replyMessage->setInReplyTo($originalMessage);

        self::assertSame($replyMessage, $result);
        self::assertSame($originalMessage, $replyMessage->getInReplyTo());
    }

    public function testToArrayWithBasicData(): void
    {
        $message = new Message();
        $message->setContent('Test message');
        $message->setUser($this->user);
        $message->setStatus(Message::STATUS_SENT);

        $array = $message->toArray();

        self::assertIsArray($array);
        self::assertArrayHasKey('id', $array);
        self::assertArrayHasKey('message', $array);
        self::assertArrayHasKey('user', $array);
        self::assertArrayHasKey('status', $array);
        self::assertArrayHasKey('created_at', $array);
        self::assertArrayHasKey('updated_at', $array);
        self::assertArrayHasKey('in_reply_to', $array);
        self::assertArrayHasKey('replies', $array);

        self::assertSame('Test message', $array['message']);
        self::assertSame('Test User', $array['user']);
        self::assertSame(Message::STATUS_SENT, $array['status']);
        self::assertNull($array['in_reply_to']);
        self::assertIsArray($array['replies']);
    }

    public function testToArrayWithInReplyTo(): void
    {
        $originalMessage = new Message();
        $originalMessage->setContent('Original');
        $originalMessage->setUser($this->user);

        $reflection = new ReflectionClass($originalMessage);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($originalMessage, 1);

        $replyMessage = new Message();
        $replyMessage->setContent('Reply');
        $replyMessage->setUser($this->user);
        $replyMessage->setInReplyTo($originalMessage);

        $array = $replyMessage->toArray();

        self::assertSame(1, $array['in_reply_to']);
    }

    public function testStatusConstants(): void
    {
        self::assertSame('sent', Message::STATUS_SENT);
        self::assertSame('received', Message::STATUS_RECEIVED);
        self::assertSame('pending', Message::STATUS_PENDING);
        self::assertSame('failed', Message::STATUS_FAILED);
    }

    public function testDefaultStatus(): void
    {
        $message = new Message();

        self::assertSame(Message::STATUS_SENT, $message->getStatus());
    }

    public function testFluentInterface(): void
    {
        $message = new Message();

        $result = $message
            ->setContent('Test')
            ->setUser($this->user)
            ->setStatus(Message::STATUS_PENDING)
        ;

        self::assertSame($message, $result);
        self::assertSame('Test', $message->getContent());
        self::assertSame($this->user, $message->getUser());
        self::assertSame(Message::STATUS_PENDING, $message->getStatus());
    }
}
