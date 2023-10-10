<?php

namespace App\UserInterface\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\User\Entity\User;
use App\UserInterface\Services\NavBar\NavBarDataProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'navbar')]
class NavBarController extends AbstractController
{
    use HomeAppAPITrait;

    #[Route('/navbar-data', name: 'navbar-data', methods: [Request::METHOD_GET])]
    public function navBarData(NavBarDataProviderInterface $navBarDataProvider): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        $navbarDTOs = $navBarDataProvider->getNavBarData($user);
        if (empty($navbarDTOs)) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }
        try {
            $normalizedResponse = $this->normalizeResponse($navbarDTOs);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        if (count($normalizedResponse) !== 3) {
            return $this->sendMultiStatusJsonResponse(['Some of the navbar is missing'], $normalizedResponse);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
