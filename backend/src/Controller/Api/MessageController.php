<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\CreateMessageDTO;
use App\Entity\User;
use App\Service\MessageService;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route('/api/messages', name: 'api_messages_')]
#[IsGranted('ROLE_USER')]
#[OA\Tag(name: 'Messages')]
final class MessageController
{
    public function __construct(
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('', name: 'get', methods: ['GET'])]
    #[OA\Get(
        path: '/api/messages',
        summary: 'Get all user messages',
        security: [['bearerAuth' => []]],
        tags: ['Messages'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of messages retrieved successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'message', type: 'string', example: 'Hello, how are you?'),
                            new OA\Property(property: 'user', type: 'string', nullable: true, example: 'John Doe'),
                            new OA\Property(property: 'status', type: 'string', enum: ['sent', 'received', 'pending', 'failed'], example: 'sent'),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-15T10:30:00+00:00'),
                            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-15T10:30:00+00:00'),
                            new OA\Property(property: 'in_reply_to', type: 'integer', nullable: true, example: null),
                            new OA\Property(
                                property: 'replies',
                                type: 'array',
                                items: new OA\Items(type: 'object'),
                                example: []
                            ),
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized - Invalid or missing token'
            )
        ]
    )]
    public function get(#[CurrentUser] User $user): JsonResponse
    {
        $messages = $this->messageService->getUserMessages($user);

        return new JsonResponse(
            array_map(static fn ($message) => $message->toArray(), $messages)
        );
    }

    #[Route('', name: 'post', methods: ['POST'])]
    #[OA\Post(
        path: '/api/messages',
        summary: 'Send a new message',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['message'],
                properties: [
                    new OA\Property(property: 'message', type: 'string', minLength: 1, maxLength: 5000, example: 'Hello, how are you?'),
                ]
            )
        ),
        tags: ['Messages'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Message created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 3),
                        new OA\Property(property: 'message', type: 'string', example: 'Hello, how are you?'),
                        new OA\Property(property: 'user', type: 'string', example: 'John Doe'),
                        new OA\Property(property: 'status', type: 'string', example: 'sent'),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-15T10:35:00+00:00'),
                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-15T10:35:00+00:00'),
                        new OA\Property(property: 'in_reply_to', type: 'integer', nullable: true, example: null),
                        new OA\Property(property: 'replies', type: 'array', items: new OA\Items(type: 'object'), example: []),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Validation error'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized - Invalid or missing token'
            )
        ]
    )]
    public function post(
        #[MapRequestPayload] CreateMessageDTO $dto,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $message = $this->messageService->createMessage($dto, $user);

        return new JsonResponse($message->toArray(), Response::HTTP_CREATED);
    }
}
