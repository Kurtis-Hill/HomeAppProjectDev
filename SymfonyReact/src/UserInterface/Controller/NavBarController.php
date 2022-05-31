<?php

namespace App\UserInterface\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\User\Entity\User;
use App\UserInterface\Exceptions\WrongUserTypeException;
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
    public function navBarData(NavBarDataProviderInterface $navBarService): JsonResponse
    {
        if (!$this->getUser() instanceof User) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        try {
            $navbarDTO = $navBarService->getNavBarData($this->getUser());
        } catch (WrongUserTypeException $e) {
            return $this->sendForbiddenAccessJsonResponse([$e->getMessage()]);
        }

        try {
            $normalizedResponse = $this->normalizeResponse($navbarDTO);
        } catch (ExceptionInterface) {
            $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        if (!empty($navbarDTO->getErrors())) {
            return $this->sendMultiStatusJsonResponse([], $normalizedResponse ?? []);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse ?? []);
    }
}
