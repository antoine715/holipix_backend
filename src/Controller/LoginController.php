<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $JWTManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return $this->json(['message' => 'Email et mot de passe requis'], 400);
        }

        // Récupération de l’utilisateur
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->json(['message' => 'Utilisateur introuvable'], 404);
        }

        // Vérification du mot de passe
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['message' => 'Mot de passe incorrect'], 401);
        }

        // Vérification si l’utilisateur a confirmé son email
        if (!$user->isVerified()) {
            return $this->json(['message' => 'Veuillez vérifier votre email avant de vous connecter'], 403);
        }

        // Détermination du rôle principal
        $roles = $user->getRoles();
        $role = in_array('ROLE_COMMERCE', $roles) ? 'commerce' : (in_array('ROLE_ADMIN', $roles) ? 'admin' : 'user');

        // Génération du token JWT
        $token = $JWTManager->create($user);

        return $this->json([
            'message' => 'Connexion réussie',
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'role' => $role,
            'token' => $token
        ]);
    }
}
