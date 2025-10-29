<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\RegisterUserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AuthenticationService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function registerUser(RegisterUserDTO $dto): User
    {
        $user = new User();
        $user->setEmail($dto->email);
        $user->setName($dto->name);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $dto->password
        );
        $user->setPassword($hashedPassword);

        $this->userRepository->save($user, true);

        return $user;
    }

    public function userExists(string $email): bool
    {
        return null !== $this->userRepository->findByEmail($email);
    }
}
