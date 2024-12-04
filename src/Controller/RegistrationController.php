<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Updated import
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface; // Updated import

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {
        // Décoder le corps de la requête JSON
        $data = json_decode($request->getContent(), true);
    
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        // Récupérer les données envoyées
        $email = $data['email'] ?? null;
        $username = $data['username'] ?? null;  // Ajout du username
        $plainPassword = $data['plainPassword'] ?? null;  // "plainPassword" correspond au champ dans votre formulaire HTML
    
        // Vérification des champs requis
        if (!$email || !$username || !$plainPassword) {
            return $this->json(['error' => 'Email, username, and password are required'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        // Validation du format de l'email
        $emailConstraint = new \Symfony\Component\Validator\Constraints\Email();
        $errors = $validator->validate($email, $emailConstraint);
        if (count($errors) > 0) {
            return $this->json(['error' => 'Invalid email format'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        // Vérifier si l'email est déjà utilisé
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return $this->json(['error' => 'Email is already in use'], JsonResponse::HTTP_CONFLICT);
        }
    
        // Vérifier si le username est déjà utilisé
        $existingUsername = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        if ($existingUsername) {
            return $this->json(['error' => 'Username is already in use'], JsonResponse::HTTP_CONFLICT);
        }
    
        // Créer l'utilisateur
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username); // Ajout du username
        $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
    
        try {
            // Sauvegarder l'utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => 'An error occurred while saving the user'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        // Générer un JWT pour l'utilisateur
        $token = $jwtManager->create($user);

        return $this->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ],
            'token' => $token,
        ], JsonResponse::HTTP_CREATED);  
    }
    
    #[Route('/register', name: 'app_register', methods: ['GET'])]
    public function affichage()
    {
        // Logique pour afficher le formulaire d'inscription
        return $this->render('registration/register.html.twig');
    }

    #[Route('/register', name: 'app_register_preflight', methods: ['OPTIONS'])]
    public function handlePreflightRequest(): Response
    {
        $response = new Response();
        $response->headers->set('Access-Control-Allow-Origin', 'https://localhost:8000');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->headers->set('Access-Control-Max-Age', '3600');

        return $response;
    }
}
