<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        // Décoder le JSON reçu et soumettre le formulaire
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            // Définir le rôle par défaut
            $user->setRoles(['ROLE_USER']);

            // Hachage du mot de passe
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Génération d'un code de vérification
            $verificationCode = bin2hex(random_bytes(3));
            $user->setVerificationCode($verificationCode);
            $user->setIsVerified(false);

            $entityManager->persist($user);
            $entityManager->flush();

            // Envoi du mail de vérification
            $email = (new Email())
                ->from('noreply@holipix.com')
                ->to($user->getEmail())
                ->subject('Verify your email')
                ->text("Your verification code: $verificationCode");

            $mailer->send($email);

            return $this->json([
                'message' => 'User created. Check your email for verification code.'
            ]);
        }

        // Retourner les erreurs si le formulaire n'est pas valide
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return $this->json(['errors' => $errors], 400);
    }

    #[Route('/verify-email', name: 'app_verify_email', methods: ['POST'])]
    public function verifyEmail(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $code = $data['code'] ?? null;

        if (!$email || !$code) {
            return $this->json(['message' => 'Email and code are required'], 400);
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user || $user->getVerificationCode() !== $code) {
            return $this->json(['message' => 'Invalid code'], 400);
        }

        $user->setIsVerified(true);
        $user->setVerificationCode(null);
        $entityManager->flush();

        return $this->json(['message' => 'Email verified successfully']);
    }
}
