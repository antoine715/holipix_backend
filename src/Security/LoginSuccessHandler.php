<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $user = $token->getUser();

        $roles = $user->getRoles();
        $role = in_array('ROLE_COMMERCE', $roles) ? 'commerce' :
                (in_array('ROLE_ADMIN', $roles) ? 'admin' : 'user');

        // Génération du JWT via LexikJWTAuthenticationBundle
        $jwt = $this->jwtManager->create($user);

        return new JsonResponse([
            'message' => 'Connexion réussie',
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'role' => $role,
            'token' => $jwt, // le vrai token JWT
        ]);
    }
}
