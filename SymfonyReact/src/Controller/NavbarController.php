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
     * @param UserInterfaceService $navbarService
     * @return JsonResponse
     */
    public function navBarData(UserInterfaceService $navbarService)
    {
        $navbarData = $navbarService->getNavBarData();

        if (!empty($navbarService->getServerErrors())) {
            return $this->sendInternelServerErrorJsonResponse($navbarService->getServerErrors());
        }

        return $this->sendSuccessfulJsonResponse($navbarData);
    }

}
