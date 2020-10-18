<?php


namespace App\Controller;


use App\PageData\LiveSensorData;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/HomeApp/WebApp")
 */
class IndexController extends AbstractController
{

    /**
     * @Route("/{route}", name="index")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request, $route) :Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('index/index.html.twig');
    }

    /**
     * @Route("/login/userdetails", name="userDetails")
     * @param Request $request
     * @return Response
     */
    public function getUserDetails(UserService $userService):  JsonResponse
    {
        if (!empty($userService->getUserErrors())) {
            return new JsonResponse(['userID' => $userService->getUserID(), 'roles' => $userService->getUserRoles()]);
        }
        else {
            dd('user errorsa');
            return new JsonResponse(['errors' => $userService->getUserErrors()]);
        }
    }
}