<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\CreateMessageDTO;
use App\Entity\User;
use App\Service\MessageService;
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
final class MessageController
{
    public function __construct(
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('', name: 'get', methods: ['GET'])]
    public function get(#[CurrentUser] User $user): JsonResponse
    {
        $messages = $this->messageService->getUserMessages($user);

        return new JsonResponse(
            array_map(static fn ($message) => $message->toArray(), $messages)
        );
    }

    #[Route('', name: 'post', methods: ['POST'])]
    public function post(
        #[MapRequestPayload] CreateMessageDTO $dto,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $message = $this->messageService->createMessage($dto, $user);

        return new JsonResponse($message->toArray(), Response::HTTP_CREATED);
    }
}
