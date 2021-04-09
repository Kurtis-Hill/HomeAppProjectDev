<?php


namespace App\Controller;

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
     * @param UserInterfaceService $userInterfaceService
     * @return JsonResponse
     */
    public function navBarData(UserInterfaceService $userInterfaceService)
    {
        $navbarData = $userInterfaceService->getNavBarData();

        if (!empty($userInterfaceService->getServerErrors() || $userInterfaceService->getFatalErrors())) {
            return $this->sendInternelServerErrorJsonResponse(array_merge($userInterfaceService->getServerErrors(), $userInterfaceService->getFatalErrors()));
        }

        return $this->sendSuccessfulJsonResponse($navbarData);
    }

}
