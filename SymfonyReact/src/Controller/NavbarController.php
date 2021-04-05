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

        $errors = $navbarService->getErrors();

        if (!empty($errors)) {
//            dd($errors);
            return $this->sendInternelServerErrorJsonResponse($errors);
        }

        return $this->sendSuccessfulJsonResponse($navbarData);
    }

}
