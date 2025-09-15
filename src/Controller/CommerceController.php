<?php

namespace App\Controller;

use App\Entity\Commerce;
use App\Entity\Room;
use App\Entity\Offer;
use App\Entity\Photo;
use App\Entity\FeaturePhare;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[Route('/api/commerce')]
class CommerceController extends AbstractController
{
    private FilesystemOperator $uploads;

    public function __construct(
        #[Autowire(service: 'local_uploads')]
        FilesystemOperator $uploads
    ) {
        $this->uploads = $uploads;
    }

    // ===================== Infos du commerce =====================
    #[Route('/me', name: 'commerce_me', methods: ['GET'])]
    public function getMyCommerce(): Response
    {
        $user = $this->getUser();
        $commerce = $user?->getCommerce();

        if (!$commerce) {
            return $this->json(['message' => 'Aucun commerce trouvé pour cet utilisateur'], 404);
        }

        return $this->json($commerce, 200, [], ['groups' => 'commerce:read']);
    }

    #[Route('/update', name: 'commerce_update', methods: ['PUT'])]
    public function updateCommerce(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $user = $this->getUser();
        $commerce = $user?->getCommerce();

        if (!$commerce) {
            return $this->json(['message' => 'Aucun commerce trouvé pour cet utilisateur'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $commerce->setName($data['name'] ?? $commerce->getName());
        $commerce->setType($data['type'] ?? $commerce->getType()); // camping, hotel, attraction
        $commerce->setAddress($data['address'] ?? $commerce->getAddress());
        $commerce->setCity($data['city'] ?? $commerce->getCity());
        $commerce->setCountry($data['country'] ?? $commerce->getCountry());
        $commerce->setPhone($data['phone'] ?? $commerce->getPhone());
        $commerce->setPhoneFixe($data['phoneFixe'] ?? $commerce->getPhoneFixe());
        $commerce->setOpeningHours($data['openingHours'] ?? $commerce->getOpeningHours());
        $commerce->setPrice($data['price'] ?? $commerce->getPrice());

        $errors = $validator->validate($commerce);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $em->flush();

        return $this->json([
            'message' => 'Commerce mis à jour avec succès',
            'commerce' => $commerce
        ], 200, [], ['groups' => 'commerce:read']);
    }

    // ===================== Gestion des chambres =====================
    #[Route('/room/add', name: 'commerce_add_room', methods: ['POST'])]
    public function addRoom(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $commerce = $user?->getCommerce();

        if (!$commerce) return $this->json(['message' => 'Aucun commerce trouvé'], 404);

        $data = json_decode($request->getContent(), true);

        $room = new Room();
        $room->setCommerce($commerce);
        $room->setName($data['name'] ?? 'Chambre');
        $room->setCapacity($data['capacity'] ?? 1);

        $em->persist($room);
        $em->flush();

        return $this->json(['message' => 'Chambre ajoutée avec succès', 'room' => $room], 201);
    }

    // ===================== Gestion des offres =====================
    #[Route('/offer/add', name: 'commerce_add_offer', methods: ['POST'])]
    public function addOffer(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $commerce = $user?->getCommerce();

        if (!$commerce) return $this->json(['message' => 'Aucun commerce trouvé'], 404);

        $data = json_decode($request->getContent(), true);

        $offer = new Offer();
        $offer->setCommerce($commerce);
        $offer->setName($data['name'] ?? 'Offre');
        $offer->setPrice($data['price'] ?? 0.0);
        $offer->setDescription($data['description'] ?? null);
        $offer->setStartTime($data['startTime'] ?? null);
        $offer->setEndTime($data['endTime'] ?? null);

        $em->persist($offer);
        $em->flush();

        return $this->json(['message' => 'Offre ajoutée avec succès', 'offer' => $offer], 201);
    }

    // ===================== Gestion des photos =====================
    #[Route('/photo/add', name: 'commerce_add_photo', methods: ['POST'])]
    public function addPhoto(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $commerce = $user?->getCommerce();

        if (!$commerce) return $this->json(['message' => 'Aucun commerce trouvé'], 404);

        $photo = new Photo();
        $photo->setCommerce($commerce);

        $uploadedFile = $request->files->get('photo');
        if ($uploadedFile) {
            $filename = uniqid() . '.' . $uploadedFile->getClientOriginalExtension();
            $stream = fopen($uploadedFile->getPathname(), 'r+');
            $this->uploads->writeStream($filename, $stream);
            if (is_resource($stream)) fclose($stream);

            $photo->setUrl('/uploads/' . $filename);
        } else {
            $photo->setUrl($request->request->get('url', ''));
        }

        $photo->setDescription($request->request->get('description', null));
        $photo->setIsMain($request->request->get('isMain', false)); // photo principale ou intermédiaire

        $em->persist($photo);
        $em->flush();

        return $this->json(['message' => 'Photo ajoutée avec succès', 'photo' => $photo], 201);
    }

    // ===================== Gestion des features =====================
    #[Route('/feature/add', name: 'commerce_add_feature', methods: ['POST'])]
    public function addFeature(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $commerce = $user?->getCommerce();

        if (!$commerce) return $this->json(['message' => 'Aucun commerce trouvé'], 404);

        $data = json_decode($request->getContent(), true);

        $feature = new FeaturePhare();
        $feature->setCommerce($commerce);
        $feature->setTitle($data['title'] ?? 'Feature');
        $feature->setDescription($data['description'] ?? null);

        $em->persist($feature);
        $em->flush();

        return $this->json(['message' => 'Feature ajoutée avec succès', 'feature' => $feature], 201);
    }

    // ===================== Voir toutes les réservations =====================
    #[Route('/reservations', name: 'commerce_reservations', methods: ['GET'])]
    public function getReservations(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $commerce = $user?->getCommerce();

        if (!$commerce) return $this->json(['message' => 'Aucun commerce trouvé'], 404);

        $reservations = $em->getRepository(Reservation::class)->findBy(['commerce' => $commerce]);

        $result = [];
        foreach ($reservations as $reservation) {
            $result[] = [
                'id' => $reservation->getId(),
                'client' => [
                    'id' => $reservation->getUser()->getId(),
                    'name' => $reservation->getUser()->getFullName(),
                    'email' => $reservation->getUser()->getEmail()
                ],
                'room' => $reservation->getRoom()?->getName(),
                'offer' => $reservation->getOffer()?->getName(),
                'startDate' => $reservation->getStartDate()?->format('Y-m-d'),
                'endDate' => $reservation->getEndDate()?->format('Y-m-d'),
                'status' => $reservation->getStatus(),
            ];
        }

        return $this->json($result, 200);
    }
}
