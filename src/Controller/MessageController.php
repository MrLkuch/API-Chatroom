<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[Route('/api/messages')]
final class MessageController extends AbstractController
{
    #[Route('', name: 'api_message_index', methods: ['GET'])]
    public function index(MessageRepository $messageRepository, SerializerInterface $serializer): JsonResponse
    {
        $messages = $messageRepository->findAll();
        $jsonContent = $serializer->serialize($messages, 'json', ['groups' => ['message:read']]);
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'api_message_new', methods: ['POST'])]
    public function new(Request $request, MessageBusInterface $messageBus, SerializerInterface $serializer): JsonResponse
    {
        $message = $serializer->deserialize($request->getContent(), Message::class, 'json');
        $messageBus->dispatch($message);

        $jsonContent = $serializer->serialize($message, 'json', ['groups' => ['message:read']]);
        return new JsonResponse($jsonContent, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: 'api_message_show', methods: ['GET'])]
    public function show(Message $message, SerializerInterface $serializer): JsonResponse
    {
        $jsonContent = $serializer->serialize($message, 'json', ['groups' => ['message:read']]);
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'api_message_edit', methods: ['PUT'])]
    public function edit(Request $request, Message $message, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $serializer->deserialize($request->getContent(), Message::class, 'json', ['object_to_populate' => $message]);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($message, 'json', ['groups' => ['message:read']]);
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'api_message_delete', methods: ['DELETE'])]
    public function delete(Message $message, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($message);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
