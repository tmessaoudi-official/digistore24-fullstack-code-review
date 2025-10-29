<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\RegisterUserDTO;
use App\Service\AuthenticationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AuthenticationController extends AbstractController
{
    public function __construct(
        private readonly AuthenticationService $authenticationService,
    ) {
    }

    #[Route('/auth/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] RegisterUserDTO $dto,
    ): JsonResponse {
        if ($this->authenticationService->userExists($dto->email)) {
            return $this->json([
                'error' => 'User with this email already exists',
            ], Response::HTTP_CONFLICT);
        }

        $this->authenticationService->registerUser($dto);

        return $this->json(null, Response::HTTP_CREATED);
    }

    #[Route('/auth/login', name: 'api_auth_login', methods: ['POST'])]
    public function login(): never
    {
        // This method is intercepted by the login firewall
        throw new \LogicException('This should never be reached.');
    }

    #[Route('/auth/logout', name: 'api_auth_logout', methods: ['POST'])]
    public function logout(): never
    {
        // This method is intercepted by the logout firewall
        throw new \LogicException('This should never be reached.');
    }
}
