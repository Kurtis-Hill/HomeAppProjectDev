<?php


namespace App\Controller\Core;


use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


 /**
  * @Route("/HomeApp/api/user", name="userDetails")

  */
class UserDataController extends AbstractController
{
    /**
     * @Route("/account-details", name="userDetails")
     * @return JsonResponse
     */
    public function getUserDetails(UserService $userService):  JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse(['errors' => $userService->getUserErrors()]);
        } elseif (!empty($userService->getUserErrors())) {
            return new JsonResponse(['errors' => $userService->getUserErrors()]);
        } else {
            return new JsonResponse(['userID' => $userService->getUserID(), 'roles' => $userService->getUserRoles()]);
        }
    }
}