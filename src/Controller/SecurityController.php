<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

class SecurityController extends AbstractController
{
    #[Route(path: '/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return $this->json(['error' => 'Email and password are required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Validate email format
        $emailConstraint = new Assert\Email();
        $emailErrors = $validator->validate($email, $emailConstraint);
        if (count($emailErrors) > 0) {
            return $this->json(['error' => 'Invalid email format'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Fetch the user
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user instanceof User || !$passwordHasher->isPasswordValid($user, plainPassword: $password)) {
            return $this->json(['error' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Generate JWT token
        $token = $jwtManager->create($user);

        return $this->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ], JsonResponse::HTTP_OK);
    }

    #[Route(path: '/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // In a stateless API, logout is handled by the client deleting the token
        return $this->json(['message' => 'Logout successful'], JsonResponse::HTTP_OK);
    }

    #[Route('/login', name: 'app_login', methods: ['GET'])]
    public function affichage()
    {
        // Logique pour afficher le formulaire d'inscription
        return $this->render('security/login.html.twig');
    }
}
