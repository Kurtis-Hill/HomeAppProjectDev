<?php


namespace App\Controller;


use App\Entity\Core\Room;
use App\Services\NavbarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @Route("/HomeApp/WebApp/navbar", name="navbar")
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

        $errors = $navbarService->getErrors();
//        dd($navbarData);

        if (empty($navbarData['rooms'])){
            $navbarData['rooms'] = 'No Rooms You May Need To Add A Room First';
            dd('1');
        }

        if (empty($navbarData['devices'])){
            $navbarData['devices'] = 'No Devices You May Need To Add A Room First';
            dd('2');
        }

//        if (!$errors) {
//            dd($errors);
//            return new JsonResponse($errors, 500);
//        }

        return new JsonResponse($navbarData);
    }

}