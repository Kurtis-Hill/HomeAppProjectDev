<?php


namespace App\Controller\UserInterface;


use App\Services\UserInterfaceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfilebarController
 * @package App\Controller
 * @Route("HomeApp/profilebar")
 */
class ProfilebarController extends AbstractController
{
//    /**
//     * @Route("/userdata")
//     */
//    public function getUserData(ProfilebarServiceUser $profilebarService)
//    {
//        $userData['name'] = $profilebarService->getFullUserName();
//        $userData['profilePic'] = $profilebarService->getProfilePic();
//        $userData['groupName'] = $profilebarService->getGroupName();
//
//        return new JsonResponse($userData);
//    }

    /**
     * @Route("/usernotifications")
     */
    public function getUserNotifications()
    {

    }
}
