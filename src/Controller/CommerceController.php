<?php

namespace App\Controller;

use App\Entity\Commerce;
use App\Entity\Room;
use App\Entity\Offer;
use App\Entity\Photo;
use App\Entity\FeaturePhare;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/commerce')]
class CommerceController extends AbstractController
{
    #[Route('/me', name: 'commerce_me', methods: ['GET'])]
    public function getMyCommerce(): Response
    {
        $user = $this->getUser();
        $commerce = $user->getCommerce();

        if (!$commerce) {
            return $this->json(['message' => 'Aucun commerce trouvé pour cet utilisateur'], 404);
        }

        return $this->json($commerce, 200, [], ['groups' => 'commerce:read']);
    }

    #[Route('/update', name: 'commerce_update', methods: ['PUT'])]
    public function updateCommerce(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $user = $this->getUser();
        $commerce = $user->getCommerce();

        if (!$commerce) {
            return $this->json(['message' => 'Aucun commerce trouvé pour cet utilisateur'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $commerce->setName($data['name'] ?? $commerce->getName());
        $commerce->setType($data['type'] ?? $commerce->getType());
        $commerce->setAddress($data['address'] ?? $commerce->getAddress());
        $commerce->setCity($data['city'] ?? $commerce->getCity());
        $commerce->setCountry($data['country'] ?? $commerce->getCountry());
        if (isset($data['phone'])) $commerce->setPhone($data['phone']);
        if (isset($data['phoneFixe'])) $commerce->setPhoneFixe($data['phoneFixe']);

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

    #[Route('/room/add', name: 'commerce_add_room', methods: ['POST'])]
    public function addRoom(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $commerce = $user->getCommerce();

        if (!$commerce) {
            return $this->json(['message' => 'Aucun commerce trouvé pour cet utilisateur'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $room = new Room();
        $room->setCommerce($commerce);
        $room->setName($data['name'] ?? 'Chambre');
        $room->setCapacity($data['capacity'] ?? 1);

        $em->persist($room);
        $em->flush();

        return $this->json([
            'message' => 'Chambre ajoutée avec succès',
            'room' => $room
        ], 201);
    }

    #[Route('/offer/add', name: 'commerce_add_offer', methods: ['POST'])]
    public function addOffer(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $commerce = $user->getCommerce();

        if (!$commerce) {
            return $this->json(['message' => 'Aucun commerce trouvé pour cet utilisateur'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $offer = new Offer();
        $offer->setCommerce($commerce);
        $offer->setName($data['name'] ?? 'Offre');
        $offer->setPrice($data['price'] ?? 0.0);

        $em->persist($offer);
        $em->flush();

        return $this->json([
            'message' => 'Offre ajoutée avec succès',
            'offer' => $offer
        ], 201);
    }

    #[Route('/photo/add', name: 'commerce_add_photo', methods: ['POST'])]
    public function addPhoto(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $commerce = $user->getCommerce();

        if (!$commerce) {
            return $this->json(['message' => 'Aucun commerce trouvé pour cet utilisateur'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $photo = new Photo();
        $photo->setCommerce($commerce);
        $photo->setUrl($data['url'] ?? '');
        $photo->setDescription($data['description'] ?? null);

        $em->persist($photo);
        $em->flush();

        return $this->json([
            'message' => 'Photo ajoutée avec succès',
            'photo' => $photo
        ], 201);
    }

    #[Route('/feature/add', name: 'commerce_add_feature', methods: ['POST'])]
    public function addFeature(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $commerce = $user->getCommerce();

        if (!$commerce) {
            return $this->json(['message' => 'Aucun commerce trouvé pour cet utilisateur'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $feature = new FeaturePhare();
        $feature->setCommerce($commerce);
        $feature->setTitle($data['title'] ?? 'Feature');
        $feature->setDescription($data['description'] ?? null);

        $em->persist($feature);
        $em->flush();

        return $this->json([
            'message' => 'Feature ajoutée avec succès',
            'feature' => $feature
        ], 201);
    }
}
