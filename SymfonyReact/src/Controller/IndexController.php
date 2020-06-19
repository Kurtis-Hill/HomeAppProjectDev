<?php


namespace App\Controller;


use App\Entity\Core\User;

use App\PageData\LiveSensorData;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;


/**
 * @Route("/HomeApp")
 */
class IndexController extends AbstractController
{

    /**
     * @Route("/index", name="index")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request) :Response
    {
        return $this->render('index/index.html.twig', [
        ]);
    }



}