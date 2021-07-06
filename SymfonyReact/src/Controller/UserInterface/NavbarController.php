<?php


namespace App\Controller\UserInterface;

use App\Services\UserInterfaceService;
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
     *
     * @param UserInterfaceService $userInterfaceService
     *
     * @return JsonResponse
     */
    public function navBarData(UserInterfaceService $userInterfaceService)
    {
        $navbarData = $userInterfaceService->getNavBarData();

        if (!empty($userInterfaceService->getServerErrors())) {
            return $this->sendInternelServerErrorJsonResponse($userInterfaceService->getServerErrors());
        }

        return $this->sendSuccessfulJsonResponse($navbarData);
    }
}
