<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Message;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserCreation(): void
    {
        $user = new User();

        $this->assertNull($user->getId());
        $this->assertNull($user->getEmail());
        $this->assertNull($user->getName());
        $this->assertIsArray($user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testSetAndGetEmail(): void
    {
        $user = new User();
        $email = 'test@example.com';

        $result = $user->setEmail($email);

        $this->assertSame($user, $result);
        $this->assertEquals($email, $user->getEmail());
    }

    public function testSetAndGetName(): void
    {
        $user = new User();
        $name = 'John Doe';

        $result = $user->setName($name);

        $this->assertSame($user, $result);
        $this->assertEquals($name, $user->getName());
    }

    public function testSetAndGetPassword(): void
    {
        $user = new User();
        $password = 'hashed_password';

        $result = $user->setPassword($password);

        $this->assertSame($user, $result);
        $this->assertEquals($password, $user->getPassword());
    }

    public function testSetAndGetRoles(): void
    {
        $user = new User();
        $roles = ['ROLE_ADMIN', 'ROLE_MODERATOR'];

        $result = $user->setRoles($roles);

        $this->assertSame($user, $result);
        $returnedRoles = $user->getRoles();
        
        $this->assertContains('ROLE_USER', $returnedRoles);
        $this->assertContains('ROLE_ADMIN', $returnedRoles);
        $this->assertContains('ROLE_MODERATOR', $returnedRoles);
    }

    public function testGetRolesAlwaysIncludesRoleUser(): void
    {
        $user = new User();
        $user->setRoles([]);

        $roles = $user->getRoles();

        $this->assertContains('ROLE_USER', $roles);
    }

    public function testGetRolesReturnsUniqueValues(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_USER']);

        $roles = $user->getRoles();

        $this->assertEquals(count($roles), count(array_unique($roles)));
    }

    public function testGetUserIdentifier(): void
    {
        $user = new User();
        $email = 'test@example.com';
        $user->setEmail($email);

        $this->assertEquals($email, $user->getUserIdentifier());
    }

    public function testAddMessage(): void
    {
        $user = new User();
        $message = new Message();
        $message->setContent('Test message');

        $result = $user->addMessage($message);

        $this->assertSame($user, $result);
        $this->assertTrue($user->getMessages()->contains($message));
        $this->assertSame($user, $message->getUser());
    }

    public function testAddMessageDoesNotDuplicateMessages(): void
    {
        $user = new User();
        $message = new Message();
        $message->setContent('Test message');

        $user->addMessage($message);
        $user->addMessage($message);

        $this->assertCount(1, $user->getMessages());
    }

    public function testRemoveMessage(): void
    {
        $user = new User();
        $message = new Message();
        $message->setContent('Test message');

        $user->addMessage($message);
        $result = $user->removeMessage($message);

        $this->assertSame($user, $result);
        $this->assertFalse($user->getMessages()->contains($message));
    }

    public function testEraseCredentials(): void
    {
        $user = new User();
        
        $user->eraseCredentials();
        
        $this->assertTrue(true);
    }
}
