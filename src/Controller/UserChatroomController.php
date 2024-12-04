<?php

namespace App\Controller;

use App\Entity\UserChatroom;
use App\Repository\UserChatroomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/user-chatrooms')]
final class UserChatroomController extends AbstractController
{
    #[Route('', name: 'api_user_chatroom_index', methods: ['GET'])]
    public function index(UserChatroomRepository $userChatroomRepository, SerializerInterface $serializer): JsonResponse
    {
        $userChatrooms = $userChatroomRepository->findAll();
        $jsonContent = $serializer->serialize($userChatrooms, 'json', ['groups' => ['userchatroom:read']]);
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'api_user_chatroom_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $userChatroom = $serializer->deserialize($request->getContent(), UserChatroom::class, 'json');
        $entityManager->persist($userChatroom);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($userChatroom, 'json', ['groups' => ['userchatroom:read']]);
        return new JsonResponse($jsonContent, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: 'api_user_chatroom_show', methods: ['GET'])]
    public function show(UserChatroom $userChatroom, SerializerInterface $serializer): JsonResponse
    {
        $jsonContent = $serializer->serialize($userChatroom, 'json', ['groups' => ['userchatroom:read']]);
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'api_user_chatroom_edit', methods: ['PUT'])]
    public function edit(Request $request, UserChatroom $userChatroom, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $serializer->deserialize($request->getContent(), UserChatroom::class, 'json', ['object_to_populate' => $userChatroom]);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($userChatroom, 'json', ['groups' => ['userchatroom:read']]);
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'api_user_chatroom_delete', methods: ['DELETE'])]
    public function delete(UserChatroom $userChatroom, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($userChatroom);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
