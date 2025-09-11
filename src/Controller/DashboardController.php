<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function dashboard(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        return $this->json([
            'message' => 'Bienvenue sur le dashboard utilisateur',
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()
        ]);
    }

    #[Route('/commerce/dashboard', name: 'commerce_dashboard', methods: ['GET'])]
    public function commerceDashboard(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        if (!in_array('ROLE_COMMERCE', $user->getRoles())) {
            return $this->json(['message' => 'Accès interdit, rôle commerce requis'], 403);
        }

        return $this->json([
            'message' => 'Bienvenue sur le dashboard commerce',
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()
        ]);
    }
}
