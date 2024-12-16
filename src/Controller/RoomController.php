<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Room;
use App\Entity\Reservation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class RoomController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function index(): Response
    {
        $rooms = $this->entityManager->getRepository(Room::class)->findAll();

        return $this->render('room/index.html.twig', [
            'rooms' => $rooms,
        ]);
    }

    public function show(Room $room): Response
    {
        return $this->render('room/show.html.twig', [
            'room' => $room,
        ]);
    }

    public function reserve(Room $room): Response
    {

        $this->addFlash('success', 'La salle a été réservée avec succès.');

        return $this->redirectToRoute('room_show', ['id' => $room->getId()]);
    }
public function getReservations(Room $room): JsonResponse
{
    $reservations = $this->entityManager->getRepository(Reservation::class)->findBy(['room' => $room]);

    $events = [];

    foreach ($reservations as $reservation) {
        $events[] = [
            'title' => 'Indisponible',
            'start' => $reservation->getStart()->format('Y-m-d\TH:i:s'),
            'end' => $reservation->getEnd()->format('Y-m-d\TH:i:s'),
            'status' => 'indisponible'
        ];
    }

    return new JsonResponse($events);
    }
}


