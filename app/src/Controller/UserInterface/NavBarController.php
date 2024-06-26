<?php

namespace App\Controller\UserInterface;

use App\Entity\User\User;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\UserInterface\NavBar\NavBarDataProviderInterface;
use App\Traits\HomeAppAPITrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
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
            $normalizedResponse = $this->normalize($navbarDTOs);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        if (count($normalizedResponse) !== 3) {
            return $this->sendMultiStatusJsonResponse(['Some of the navbar is missing'], $normalizedResponse);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
