<?php

namespace App\UserInterface\Controller;

use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\User\Entity\User;
use App\UserInterface\Services\UserData\UserDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-data/', name: 'home')]
class GetUserDataController extends AbstractController
{
    use HomeAppAPITrait;
    #[Route('get', name: 'get-user-data', methods: [Request::METHOD_GET])]
    public function getGeneralUserData(UserDataProvider $userDataProvider): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendInternalServerErrorJsonResponse(['User is not logged in'], );
        }

        $userData = $userDataProvider->getGeneralUserData($user);

        if (!empty($userDataProvider->getProcessErrors())) {
            $this->sendBadRequestJsonResponse($userDataProvider->getProcessErrors());
        }

        $userDataResponse = $this->normalizeResponse($userData);

        return $this->sendSuccessfulJsonResponse($userDataResponse);
    }
}
