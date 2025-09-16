<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Reservation;
use App\Entity\Commerce;
use App\Entity\Room;
use App\Entity\Offer;
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

        // Optionnel : associer une offre si fournie
        $offer = isset($data['offer']) ? $em->getRepository(Offer::class)->find($data['offer']) : null;

        // Assigner une chambre disponible automatiquement
        $room = $em->getRepository(Room::class)->findOneBy(['commerce' => $commerce]);
        if (!$room) return $this->json(['message' => 'Aucune chambre disponible dans ce commerce'], 404);

        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setCommerce($commerce);
        $reservation->setRoom($room);
        $reservation->setOffer($offer);
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
                'room' => [
                    'id' => $room->getId(),
                    'name' => $room->getName(),
                    'capacity' => $room->getCapacity(),
                ],
                'offer' => $offer ? [
                    'id' => $offer->getId(),
                    'name' => $offer->getName(),
                ] : null
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
                'room' => $res->getRoom() ? [
                    'id' => $res->getRoom()->getId(),
                    'name' => $res->getRoom()->getName(),
                    'capacity' => $res->getRoom()->getCapacity(),
                ] : null,
                'offer' => $res->getOffer() ? [
                    'id' => $res->getOffer()->getId(),
                    'name' => $res->getOffer()->getName(),
                ] : null,
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

    // ===================== Ajouter / annuler / lister les photos =====================
    // (reste identique à ton précédent ClientController)
}
