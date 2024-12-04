<?php

namespace App\Controller;

use App\Entity\Chatroom;
use App\Repository\ChatroomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/chatrooms')]
final class ChatroomController extends AbstractController
{
    #[Route('', name: 'api_chatroom_index', methods: ['GET'])]
    public function index(ChatroomRepository $chatroomRepository, SerializerInterface $serializer): JsonResponse
    {
        $chatrooms = $chatroomRepository->findAll();
        $jsonContent = $serializer->serialize($chatrooms, 'json', ['groups' => ['chatroom:read']]);
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'api_chatroom_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $chatroom = $serializer->deserialize($request->getContent(), Chatroom::class, 'json');
        $entityManager->persist($chatroom);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($chatroom, 'json', ['groups' => ['chatroom:read']]);
        return new JsonResponse($jsonContent, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: 'api_chatroom_show', methods: ['GET'])]
    public function show(Chatroom $chatroom, SerializerInterface $serializer): JsonResponse
    {
        $jsonContent = $serializer->serialize($chatroom, 'json', ['groups' => ['chatroom:read']]);
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'api_chatroom_edit', methods: ['PUT'])]
    public function edit(Request $request, Chatroom $chatroom, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $serializer->deserialize($request->getContent(), Chatroom::class, 'json', ['object_to_populate' => $chatroom]);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($chatroom, 'json', ['groups' => ['chatroom:read']]);
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'api_chatroom_delete', methods: ['DELETE'])]
    public function delete(Chatroom $chatroom, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($chatroom);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
