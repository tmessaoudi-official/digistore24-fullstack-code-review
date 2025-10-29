<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\RegisterUserDTO;
use App\Service\AuthenticationService;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/api/auth', name: 'api_auth_')]
class AuthenticationController
{
    public function __construct(
        private readonly AuthenticationService $authenticationService,
    ) {
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] RegisterUserDTO $dto,
    ): JsonResponse {
        if ($this->authenticationService->userExists($dto->email)) {
            return new JsonResponse([
                'error' => 'User with this email already exists',
            ], Response::HTTP_CONFLICT);
        }

        $this->authenticationService->registerUser($dto);

        return new JsonResponse('', Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(): never
    {
        // This method is intercepted by the login firewall
        throw new LogicException('This should never be reached.');
    }

    #[Route('/logout', name: 'logout', methods: ['POST', 'GET'])]
    public function logout(): never
    {
        // This method is intercepted by the logout firewall
        throw new LogicException('This should never be reached.');
    }
}
