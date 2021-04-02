<?php


namespace App\Controller\Core;


use App\Services\UserServiceUser;
use App\Traits\API\HomeAppAPIResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


 /**
  * @Route("/HomeApp/api/user", name="userDetails")

  */
class UserDataController extends AbstractController
{
    use HomeAppAPIResponseTrait;
    /**
     * @Route("/account-details", name="userDetails")
     * @param UserServiceUser $userService
     * @return JsonResponse
     */
    public function getUserDetails(UserServiceUser $userService):  JsonResponse
    {
        $userData = $userService->getAppUserDataForLocalStorage();

        if (!empty($userService->getFatalErrors())) {
            return $this->sendInternelServerErrorResponse($userService->getFatalErrors());
       }

        return $this->sendSuccessfulJsonResponse($userData);
    }
}
