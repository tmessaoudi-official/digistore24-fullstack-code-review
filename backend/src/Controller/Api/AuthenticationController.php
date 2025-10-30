<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\RegisterUserDTO;
use App\Entity\User;
use App\Service\AuthenticationService;
use LogicException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route('/api/auth', name: 'api_auth_')]
#[OA\Tag(name: 'Authentication')]
class AuthenticationController
{
    public function __construct(
        private readonly AuthenticationService $authenticationService,
    ) {
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    #[OA\Post(
        path: '/api/auth/register',
        summary: 'Register a new user',
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password', 'name'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john.doe@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'SecurePass123!'),
                    new OA\Property(property: 'name', type: 'string', minLength: 2, maxLength: 255, example: 'John Doe'),
                ]
            )
        ),
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'User successfully registered'
            ),
            new OA\Response(
                response: 409,
                description: 'User with this email already exists',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'User with this email already exists')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Validation error'
            )
        ]
    )]
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

    #[IsGranted('ROLE_USER')]
    #[Route('/me', name: 'me', methods: ['GET'])]
    #[OA\Get(
        path: '/api/auth/me',
        summary: 'Get current user information',
        security: [['bearerAuth' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User information retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'email', type: 'string', example: 'john.doe@example.com'),
                        new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                        new OA\Property(
                            property: 'roles',
                            type: 'array',
                            items: new OA\Items(type: 'string'),
                            example: ['ROLE_USER']
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized - Invalid or missing token'
            )
        ]
    )]
    public function me(#[CurrentUser] User $user): JsonResponse
    {
        return new JsonResponse([
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    #[OA\Post(
        path: '/api/auth/login',
        summary: 'Login user',
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john.doe@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'SecurePass123!'),
                ]
            )
        ),
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'token', type: 'string', example: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...')
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Invalid credentials'
            )
        ]
    )]
    public function login(): never
    {
        // This method is intercepted by the login firewall
        throw new LogicException('This should never be reached.');
    }

    #[Route('/logout', name: 'logout', methods: ['POST', 'GET'])]
    #[OA\Post(
        path: '/api/auth/logout',
        summary: 'Logout user',
        security: [['bearerAuth' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout successful'
            )
        ]
    )]
    #[OA\Get(
        path: '/api/auth/logout',
        summary: 'Logout user (GET)',
        security: [['bearerAuth' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout successful'
            )
        ]
    )]
    public function logout(): never
    {
        // This method is intercepted by the logout firewall
        throw new LogicException('This should never be reached.');
    }
}
