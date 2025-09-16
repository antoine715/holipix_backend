<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Reservation;
use App\Entity\Commerce;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

#[Route('/api/client')]
class ClientController extends AbstractController
{
    private FilesystemOperator $uploads;
    private MailerInterface $mailer;

    public function __construct(
        #[Autowire(service: 'local_uploads')]
        FilesystemOperator $uploads,
        MailerInterface $mailer
    ) {
        $this->uploads = $uploads;
        $this->mailer = $mailer;
    }

    // ===================== Créer une réservation =====================
    #[Route('/reservation/create', name: 'client_create_reservation', methods: ['POST'])]
    public function createReservation(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->json(['message' => 'Utilisateur non authentifié'], 401);

        $data = json_decode($request->getContent(), true);

        $commerce = $em->getRepository(Commerce::class)->find($data['commerce']);
        if (!$commerce) return $this->json(['message' => 'Commerce non trouvé'], 404);

        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setCommerce($commerce);
        $reservation->setDateArrivee(new \DateTimeImmutable($data['dateArrivee']));
        $reservation->setDateDepart(new \DateTimeImmutable($data['dateDepart']));
        $reservation->setNombreAdultes($data['nombreAdultes'] ?? 1);
        $reservation->setNombreEnfants($data['nombreEnfants'] ?? 0);
        $reservation->setNombreChambres($data['nombreChambres'] ?? 1);
        $reservation->setTotal($data['total'] ?? 0);

        $em->persist($reservation);
        $em->flush();

        // Envoi de l'email de confirmation
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@holipix.com', 'Holipix'))
            ->to($user->getEmail())
            ->subject('Confirmation de votre réservation')
            ->htmlTemplate('emails/reservation_confirmation.html.twig')
            ->context([
                'user' => $user,
                'reservation' => $reservation,
            ]);

        $this->mailer->send($email);

        return $this->json([
            'message' => 'Réservation créée avec succès et email envoyé',
            'reservation' => [
                'id' => $reservation->getId(),
                'dateArrivee' => $reservation->getDateArrivee()->format('Y-m-d'),
                'dateDepart' => $reservation->getDateDepart()->format('Y-m-d'),
                'total' => $reservation->getTotal(),
            ]
        ], 201);
    }

    // ===================== Lister les réservations =====================
    #[Route('/reservations', name: 'client_list_reservations', methods: ['GET'])]
    public function listReservations(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->json(['message' => 'Utilisateur non authentifié'], 401);

        $reservations = $em->getRepository(Reservation::class)->findBy(['user' => $user]);
        $result = [];

        foreach ($reservations as $res) {
            $result[] = [
                'id' => $res->getId(),
                'commerce' => [
                    'id' => $res->getCommerce()->getId(),
                    'name' => $res->getCommerce()->getName(),
                    'type' => $res->getCommerce()->getType(),
                    'address' => $res->getCommerce()->getAddress(),
                    'phone' => $res->getCommerce()->getPhone(),
                ],
                'startDate' => $res->getDateArrivee()?->format('Y-m-d'),
                'endDate' => $res->getDateDepart()?->format('Y-m-d'),
                'photos' => array_map(fn(Photo $p) => [
                    'id' => $p->getId(),
                    'url' => $p->getUrl(),
                    'description' => $p->getDescription(),
                    'validated' => $p->isValidated()
                ], $res->getPhotos()->toArray())
            ];
        }

        return $this->json($result);
    }

    // ===================== Ajouter une photo =====================
    #[Route('/reservation/{id}/photo', name: 'client_add_photo', methods: ['POST'])]
    public function addPhoto(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->json(['message' => 'Utilisateur non authentifié'], 401);

        $reservation = $em->getRepository(Reservation::class)->find($id);
        if (!$reservation || $reservation->getUser() !== $user) {
            return $this->json(['message' => 'Réservation invalide'], 404);
        }

        $today = new \DateTime();
        if ($today < $reservation->getDateArrivee() || $today > $reservation->getDateDepart()) {
            return $this->json(['message' => 'Vous ne pouvez ajouter une photo que pendant votre séjour'], 403);
        }

        $uploadedFile = $request->files->get('photo');
        if (!$uploadedFile) return $this->json(['message' => 'Aucun fichier uploadé'], 400);

        $filename = uniqid() . '.' . $uploadedFile->getClientOriginalExtension();
        $stream = fopen($uploadedFile->getPathname(), 'r+');
        $this->uploads->writeStream($filename, $stream);
        if (is_resource($stream)) fclose($stream);

        $photo = new Photo();
        $photo->setUser($user);
        $photo->setReservation($reservation);
        $photo->setCommerce($reservation->getCommerce());
        $photo->setUrl('/uploads/' . $filename);
        $photo->setDescription($request->request->get('description', null));
        $photo->setValidated(false);

        $em->persist($photo);
        $em->flush();

        return $this->json([
            'message' => 'Photo ajoutée temporairement. Vous pouvez annuler avant validation.',
            'photo' => [
                'id' => $photo->getId(),
                'url' => $photo->getUrl(),
                'description' => $photo->getDescription(),
                'validated' => $photo->isValidated()
            ]
        ], 201);
    }

    // ===================== Annuler une photo =====================
    #[Route('/photo/{id}/cancel', name: 'client_cancel_photo', methods: ['DELETE'])]
    public function cancelPhoto(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->json(['message' => 'Utilisateur non authentifié'], 401);

        $photo = $em->getRepository(Photo::class)->find($id);
        if (!$photo || $photo->getUser() !== $user || $photo->isValidated()) {
            return $this->json(['message' => 'Photo non trouvée ou déjà validée'], 404);
        }

        $em->remove($photo);
        $em->flush();

        return $this->json(['message' => 'Photo annulée avec succès']);
    }

    // ===================== Lister les photos d'une réservation =====================
    #[Route('/reservation/{id}/photos', name: 'client_list_photos', methods: ['GET'])]
    public function listPhotos(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $reservation = $em->getRepository(Reservation::class)->find($id);

        if (!$reservation || $reservation->getUser() !== $user) {
            return $this->json(['message' => 'Réservation invalide'], 404);
        }

        $photos = $reservation->getPhotos();
        $result = [];

        foreach ($photos as $photo) {
            $result[] = [
                'id' => $photo->getId(),
                'url' => $photo->getUrl(),
                'description' => $photo->getDescription(),
                'validated' => $photo->isValidated()
            ];
        }

        return $this->json($result);
    }
    // ===================== Détails d'une réservation =====================
#[Route('/reservations/{id}', name: 'client_get_reservation', methods: ['GET'])]
public function getReservation(int $id, EntityManagerInterface $em): Response
{
    $user = $this->getUser();
    if (!$user) {
        return $this->json(['message' => 'Utilisateur non authentifié'], 401);
    }

    $reservation = $em->getRepository(Reservation::class)->find($id);
    if (!$reservation || $reservation->getUser() !== $user) {
        return $this->json(['message' => 'Réservation introuvable'], 404);
    }

    $result = [
        'id' => $reservation->getId(),
        'commerce' => [
            'id' => $reservation->getCommerce()->getId(),
            'name' => $reservation->getCommerce()->getName(),
            'type' => $reservation->getCommerce()->getType(),
            'address' => $reservation->getCommerce()->getAddress(),
            'phone' => $reservation->getCommerce()->getPhone(),
        ],
        'startDate' => $reservation->getDateArrivee()?->format('Y-m-d'),
        'endDate' => $reservation->getDateDepart()?->format('Y-m-d'),
        'nombreAdultes' => $reservation->getNombreAdultes(),
        'nombreEnfants' => $reservation->getNombreEnfants(),
        'nombreChambres' => $reservation->getNombreChambres(),
        'total' => $reservation->getTotal(),
        'photos' => array_map(fn(Photo $p) => [
            'id' => $p->getId(),
            'url' => $p->getUrl(),
            'description' => $p->getDescription(),
            'validated' => $p->isValidated()
        ], $reservation->getPhotos()->toArray())
    ];

    return $this->json($result);
}

}
