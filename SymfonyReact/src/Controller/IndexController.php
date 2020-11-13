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
 *
 * whole app needs to handle exceptions and all the status codes properly, just getting the app on its feat first
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

}