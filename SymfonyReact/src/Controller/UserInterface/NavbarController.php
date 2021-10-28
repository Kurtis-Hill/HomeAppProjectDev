<?php


namespace App\Controller\UserInterface;

use App\Services\UserInterfaceService;
use App\Traits\API\HomeAppAPIResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @Route("/HomeApp/api/navbar", name="navbar")
 */
#[Route('/HomeApp/api/navbar', name: 'navbar')]
class NavbarController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * @param UserInterfaceService $userInterfaceService
     *
     * @return JsonResponse
     */
    #[Route('/navbar-data', name: 'navbar-data', methods: [Request::METHOD_GET])]
    public function navBarData(UserInterfaceService $userInterfaceService)
    {
        $navbarData = $userInterfaceService->getNavBarData();

        if (!empty($userInterfaceService->getServerErrors())) {
            return $this->sendInternalServerErrorJsonResponse($userInterfaceService->getServerErrors());
        }

        return $this->sendSuccessfulJsonResponse($navbarData);
    }
}
