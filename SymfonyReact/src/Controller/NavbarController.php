<?php


namespace App\Controller;

use App\Services\NavbarService;
use App\Traits\API\HomeAppAPIResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @Route("/HomeApp/api/navbar", name="navbar")
 */
class NavbarController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * @Route("/navbar-data", name="navbar-data")
     * @param NavbarService $navbarService
     * @return JsonResponse
     */
    public function navBarData(NavbarService $navbarService)
    {
        $navbarData['rooms'] = $navbarService->getUsersRooms();

        $navbarData['devices'] = $navbarService->getUserDevices();

        $navbarData['groupNames'] = $navbarService->getUsersGroupNames();

        $errors = $navbarService->getErrors();

        if (!empty($errors)) {
            return $this->sendInternelServerErrorResponse($errors);
        }

        return $this->sendSuccessfulResponse($navbarData);
    }

}