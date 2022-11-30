<?php

namespace App\UserInterface\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\User\Entity\User;
use App\UserInterface\Exceptions\WrongUserTypeException;
use App\UserInterface\Services\NavBar\NavBarDataProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'navbar')]
class NavBarController extends AbstractController
{
    use HomeAppAPITrait;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $elasticLogger)
    {
        $this->logger = $elasticLogger;
    }

    #[Route('/navbar-data', name: 'navbar-data', methods: [Request::METHOD_GET])]
    public function navBarData(NavBarDataProviderInterface $navBarDataProvider): JsonResponse
    {
        if (!$this->getUser() instanceof User) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        $navbarDTO = $navBarDataProvider->getNavBarData($this->getUser());
        try {
            $normalizedResponse = $this->normalizeResponse($navbarDTO);
        } catch (ExceptionInterface) {
            $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        if (!empty($navbarDTO->getErrors()) || !empty($navBarDataProvider->getNavbarRequestErrors())) {
            $this->logger->error('nav bar errors', ['errors' => $navbarDTO->getErrors(), 'user' => $this->getUser()->getUserIdentifier()]);

            return $this->sendMultiStatusJsonResponse(array_merge($navBarDataProvider->getNavbarRequestErrors(), $navbarDTO->getErrors()), $normalizedResponse ?? []);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse ?? []);
    }
}
