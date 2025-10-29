<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Message;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
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

        $this->assertNull($message->getId());
        $this->assertNull($message->getContent());
        $this->assertNull($message->getUser());
        $this->assertEquals(Message::STATUS_SENT, $message->getStatus());
        $this->assertNull($message->getInReplyTo());
    }

    public function testSetAndGetContent(): void
    {
        $message = new Message();
        $content = 'Hello, World!';

        $result = $message->setContent($content);

        $this->assertSame($message, $result);
        $this->assertEquals($content, $message->getContent());
    }

    public function testSetAndGetUser(): void
    {
        $message = new Message();

        $result = $message->setUser($this->user);

        $this->assertSame($message, $result);
        $this->assertSame($this->user, $message->getUser());
    }

    public function testSetAndGetStatus(): void
    {
        $message = new Message();

        $result = $message->setStatus(Message::STATUS_RECEIVED);

        $this->assertSame($message, $result);
        $this->assertEquals(Message::STATUS_RECEIVED, $message->getStatus());
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

        $this->assertSame($replyMessage, $result);
        $this->assertSame($originalMessage, $replyMessage->getInReplyTo());
    }

    public function testToArrayWithBasicData(): void
    {
        $message = new Message();
        $message->setContent('Test message');
        $message->setUser($this->user);
        $message->setStatus(Message::STATUS_SENT);

        $array = $message->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('message', $array);
        $this->assertArrayHasKey('user', $array);
        $this->assertArrayHasKey('status', $array);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);
        $this->assertArrayHasKey('in_reply_to', $array);
        $this->assertArrayHasKey('replies', $array);

        $this->assertEquals('Test message', $array['message']);
        $this->assertEquals('Test User', $array['user']);
        $this->assertEquals(Message::STATUS_SENT, $array['status']);
        $this->assertNull($array['in_reply_to']);
        $this->assertIsArray($array['replies']);
    }

    public function testToArrayWithInReplyTo(): void
    {
        $originalMessage = new Message();
        $originalMessage->setContent('Original');
        $originalMessage->setUser($this->user);

        $reflection = new \ReflectionClass($originalMessage);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($originalMessage, 1);

        $replyMessage = new Message();
        $replyMessage->setContent('Reply');
        $replyMessage->setUser($this->user);
        $replyMessage->setInReplyTo($originalMessage);

        $array = $replyMessage->toArray();

        $this->assertEquals(1, $array['in_reply_to']);
    }

    public function testStatusConstants(): void
    {
        $this->assertEquals('sent', Message::STATUS_SENT);
        $this->assertEquals('received', Message::STATUS_RECEIVED);
        $this->assertEquals('pending', Message::STATUS_PENDING);
        $this->assertEquals('failed', Message::STATUS_FAILED);
    }

    public function testDefaultStatus(): void
    {
        $message = new Message();

        $this->assertEquals(Message::STATUS_SENT, $message->getStatus());
    }

    public function testFluentInterface(): void
    {
        $message = new Message();

        $result = $message
            ->setContent('Test')
            ->setUser($this->user)
            ->setStatus(Message::STATUS_PENDING);

        $this->assertSame($message, $result);
        $this->assertEquals('Test', $message->getContent());
        $this->assertSame($this->user, $message->getUser());
        $this->assertEquals(Message::STATUS_PENDING, $message->getStatus());
    }
}
