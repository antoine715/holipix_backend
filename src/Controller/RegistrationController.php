<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Commerce;
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
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $data = json_decode($request->getContent(), true);

        $email           = $data['email'] ?? null;
        $password        = $data['password'] ?? null;
        $confirmPassword = $data['confirmPassword'] ?? null;

        if (!$email || !$password || !$confirmPassword) {
            return $this->json([
                'message' => 'Email, password et confirmPassword sont obligatoires'
            ], 400);
        }

        if ($password !== $confirmPassword) {
            return $this->json([
                'message' => 'Les mots de passe ne correspondent pas'
            ], 400);
        }

        // Vérification de la force du mot de passe
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        if (!preg_match($pattern, $password)) {
            return $this->json([
                'message' => 'Mot de passe invalide'
            ], 400);
        }

        // Vérification si email déjà existant
        if ($em->getRepository(User::class)->findOneBy(['email' => $email])) {
            return $this->json([
                'message' => 'Email déjà utilisé'
            ], 400);
        }

        // Création de l’utilisateur
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($passwordHasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_USER']);
        $user->setVerificationCode(bin2hex(random_bytes(3)));
        $user->setIsVerified(false);

        $em->persist($user);
        $em->flush();

        // Envoi email de vérification
        $emailMessage = (new Email())
            ->from('noreply@holipix.com')
            ->to($user->getEmail())
            ->subject('Vérifiez votre email')
            ->text("Code de vérification : " . $user->getVerificationCode());

        $mailer->send($emailMessage);

        return $this->json([
            'message' => 'Utilisateur créé. Vérifiez votre email.',
            'user_id' => $user->getId(),
        ]);
    }

    #[Route('/register/commerce', name: 'app_register_commerce', methods: ['POST'])]
    public function registerCommerce(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $data = json_decode($request->getContent(), true);

        $email           = $data['email'] ?? null;
        $password        = $data['password'] ?? null;
        $confirmPassword = $data['confirmPassword'] ?? null;

        if (!$email || !$password || !$confirmPassword) {
            return $this->json([
                'message' => 'Email, password et confirmPassword obligatoires'
            ], 400);
        }

        if ($password !== $confirmPassword) {
            return $this->json([
                'message' => 'Les mots de passe ne correspondent pas'
            ], 400);
        }

        // Vérification de la force du mot de passe
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        if (!preg_match($pattern, $password)) {
            return $this->json([
                'message' => 'Mot de passe invalide'
            ], 400);
        }

        // Vérification si email déjà existant
        if ($em->getRepository(User::class)->findOneBy(['email' => $email])) {
            return $this->json([
                'message' => 'Email déjà utilisé'
            ], 400);
        }

        // Création de l’utilisateur commerce
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($passwordHasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_COMMERCE']);
        $user->setVerificationCode(bin2hex(random_bytes(3)));
        $user->setIsVerified(false);

        // Création du commerce
        $commerce = new Commerce();
        $commerce->setCommercant($user);
        $commerce->setName($data['nom'] ?? '');
        $commerce->setType($data['type'] ?? '');
        $commerce->setCountry($data['pays'] ?? '');
        $commerce->setCity($data['ville'] ?? '');
        $commerce->setAddress($data['adresse'] ?? '');
        $commerce->setPhone($data['telephone'] ?? '');
        $commerce->setPhoneFixe($data['telephoneFixe'] ?? null);
        $commerce->setCreatedAt(new \DateTimeImmutable());

        $em->persist($user);
        $em->persist($commerce);
        $em->flush();

        // Envoi email de vérification
        $emailMessage = (new Email())
            ->from('noreply@holipix.com')
            ->to($user->getEmail())
            ->subject('Vérifiez votre email')
            ->text("Code de vérification : " . $user->getVerificationCode());

        $mailer->send($emailMessage);

        return $this->json([
            'message'     => 'Commerce créé. Vérifiez votre email.',
            'user_id'     => $user->getId(),
            'commerce_id' => $commerce->getId(),
        ]);
    }
        #[Route('/verify-email', name: 'app_verify_email', methods: ['POST'])]
        public function verifyEmail(Request $request, EntityManagerInterface $em): Response
        {
            $data = json_decode($request->getContent(), true);

            $email = $data['email'] ?? null;
            $code  = $data['code'] ?? null;

            if (!$email || !$code) {
                return $this->json(['message' => 'Email et code sont obligatoires'], 400);
            }

            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                return $this->json(['message' => 'Utilisateur non trouvé'], 404);
            }

            if ($user->isVerified()) {
                return $this->json(['message' => 'Utilisateur déjà vérifié'], 400);
            }

            if ($user->getVerificationCode() !== $code) {
                return $this->json(['message' => 'Code invalide'], 400);
            }

            // Validation réussie
            $user->verify(); // met isVerified à true, efface le code et définit verifiedAt
            $em->flush();

            return $this->json([
                'message' => 'Email vérifié avec succès',
                'user_id' => $user->getId(),
                'verified_at' => $user->getVerifiedAt()->format('Y-m-d H:i:s')
            ]);
        }

}
