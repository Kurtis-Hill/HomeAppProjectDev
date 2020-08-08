<?php


namespace App\Controller;


use App\Entity\Core\Room;
use App\Services\NavbarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/HomeApp/Navbar", name="navbar")
 */
class NavbarController extends AbstractController
{
    /**
     * @Route("/rooms", name="userRooms")
     * @param NavbarService $navbarService
     * @return JsonResponse
     */
    public function getAllRooms(NavbarService $navbarService)
    {
        $rooms = $navbarService->getUsersRooms();

        return new JsonResponse($rooms);
    }

    /**
     * @Route("/SensorsByRoom")
     * @param NavbarService $navbarService
     * @return JsonResponse
     */
    public function getAllSensorsByRoom(NavbarService $navbarService)
    {
        $rooms = $navbarService->getAllSensorsByRoom();

        return new JsonResponse($rooms);
    }


}