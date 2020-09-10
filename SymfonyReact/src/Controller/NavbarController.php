<?php


namespace App\Controller;


use App\Entity\Core\Room;
use App\Services\NavbarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/HomeApp/navbar", name="navbar")
 */
class NavbarController extends AbstractController
{
    /**
     * @Route("/navbar-data", name="navbar-data")
     * @param NavbarService $navbarService
     * @return JsonResponse
     */
    public function navBarData(NavbarService $navbarService)
    {
        $navbarData = [];

        $navbarData['rooms'] = $navbarService->getUsersRooms();

        $navbarData['devices'] = $navbarService->getUserDevices();

        $navbarData['groupNames'] = $navbarService->getUsersGroupNames();

        if (empty($navbarData['rooms'])){
            $navbarData['rooms'] = 'No Rooms You May Need To Add A Room First';
        }

        if (empty($navbarData['devices'])){
            $navbarData['devices'] = 'No Devices You May Need To Add A Room First';
        }


        return new JsonResponse($navbarData);
    }

}