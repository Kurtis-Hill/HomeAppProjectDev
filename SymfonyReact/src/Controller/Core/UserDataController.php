<?php


namespace App\Controller\Core;

use App\Services\UserInterfaceService;
use App\Traits\API\HomeAppAPIResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/HomeApp/api/user', name: 'user')]
class UserDataController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * @param UserInterfaceService $userService
     * @return JsonResponse
     */
    #[Route('/account-details', name: 'user-details', methods: [Request::METHOD_GET])]
    public function getUserDetails(UserInterfaceService $userService):  JsonResponse
    {
        $userData = $userService->getAppUserDataForLocalStorage();

        if (!empty($userService->getServerErrors())) {
            return $this->sendInternelServerErrorJsonResponse($userService->getServerErrors());
        }

        return $this->sendSuccessfulJsonResponse($userData);
    }
}
