<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[Route('/api/client')]
class ClientController extends AbstractController
{
    private FilesystemOperator $uploads;

    public function __construct(
        #[Autowire(service: 'local_uploads')]
        FilesystemOperator $uploads
    ) {
        $this->uploads = $uploads;
    }

    // 🔹 Lister toutes les réservations du client avec infos commerce
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
                'startDate' => $res->getStartDate()?->format('Y-m-d'),
                'endDate' => $res->getEndDate()?->format('Y-m-d'),
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

    // 🔹 Ajouter une photo pendant le séjour
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
        if ($today < $reservation->getStartDate() || $today > $reservation->getEndDate()) {
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
        $photo->setValidated(false); // à valider par admin pour remboursement

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

    // 🔹 Annuler une photo avant validation
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

    // 🔹 Lister les photos d'une réservation
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
}
