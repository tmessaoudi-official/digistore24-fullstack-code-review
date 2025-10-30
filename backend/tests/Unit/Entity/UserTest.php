<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use function count;

use App\Entity\Message;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class UserTest extends TestCase
{
    public function testUserCreation(): void
    {
        $user = new User();

        self::assertNull($user->getId());
        self::assertNull($user->getEmail());
        self::assertNull($user->getName());
        self::assertIsArray($user->getRoles());
        self::assertContains('ROLE_USER', $user->getRoles());
    }

    public function testSetAndGetEmail(): void
    {
        $user = new User();
        $email = 'test@example.com';

        $result = $user->setEmail($email);

        self::assertSame($user, $result);
        self::assertSame($email, $user->getEmail());
    }

    public function testSetAndGetName(): void
    {
        $user = new User();
        $name = 'John Doe';

        $result = $user->setName($name);

        self::assertSame($user, $result);
        self::assertSame($name, $user->getName());
    }

    public function testSetAndGetPassword(): void
    {
        $user = new User();
        $password = 'hashed_password';

        $result = $user->setPassword($password);

        self::assertSame($user, $result);
        self::assertSame($password, $user->getPassword());
    }

    public function testSetAndGetRoles(): void
    {
        $user = new User();
        $roles = ['ROLE_ADMIN', 'ROLE_MODERATOR'];

        $result = $user->setRoles($roles);

        self::assertSame($user, $result);
        $returnedRoles = $user->getRoles();

        self::assertContains('ROLE_USER', $returnedRoles);
        self::assertContains('ROLE_ADMIN', $returnedRoles);
        self::assertContains('ROLE_MODERATOR', $returnedRoles);
    }

    public function testGetRolesAlwaysIncludesRoleUser(): void
    {
        $user = new User();
        $user->setRoles([]);

        $roles = $user->getRoles();

        self::assertContains('ROLE_USER', $roles);
    }

    public function testGetRolesReturnsUniqueValues(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_USER']);

        $roles = $user->getRoles();

        self::assertSame(count($roles), count(array_unique($roles)));
    }

    public function testGetUserIdentifier(): void
    {
        $user = new User();
        $email = 'test@example.com';
        $user->setEmail($email);

        self::assertSame($email, $user->getUserIdentifier());
    }

    public function testAddMessage(): void
    {
        $user = new User();
        $message = new Message();
        $message->setContent('Test message');

        $result = $user->addMessage($message);

        self::assertSame($user, $result);
        self::assertTrue($user->getMessages()->contains($message));
        self::assertSame($user, $message->getUser());
    }

    public function testAddMessageDoesNotDuplicateMessages(): void
    {
        $user = new User();
        $message = new Message();
        $message->setContent('Test message');

        $user->addMessage($message);
        $user->addMessage($message);

        self::assertCount(1, $user->getMessages());
    }

    public function testRemoveMessage(): void
    {
        $user = new User();
        $message = new Message();
        $message->setContent('Test message');

        $user->addMessage($message);
        $result = $user->removeMessage($message);

        self::assertSame($user, $result);
        self::assertFalse($user->getMessages()->contains($message));
    }

    public function testEraseCredentials(): void
    {
        $user = new User();

        $user->eraseCredentials();

        self::assertTrue(true);
    }
}
