<?php

namespace App\UserInterface\Controller;

use App\API\CommonURL;
use App\API\Traits\HomeAppAPITrait;
use App\UserInterface\Exceptions\WrongUserTypeException;
use App\UserInterface\Services\NavBar\NavBarServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'navbar')]
class NavBarController extends AbstractController
{
    use HomeAppAPITrait;

    #[Route('/navbar-data', name: 'navbar-data', methods: [Request::METHOD_GET])]
    public function navBarData(NavBarServiceInterface $navBarService): JsonResponse
    {
        try {
            $navbarData = $navBarService->getNavBarData($this->getUser());
        } catch (WrongUserTypeException $e) {
            return $this->sendForbiddenAccessJsonResponse([$e->getMessage()]);
        }

        if (!empty($navbarData['errors'])) {
            return $this->sendMultiStatusJsonResponse($navbarData);
        }

        return $this->sendSuccessfulJsonResponse($navbarData);
    }
}
