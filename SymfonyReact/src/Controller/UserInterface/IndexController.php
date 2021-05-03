<?php

namespace App\Controller\UserInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("/HomeApp/WebApp")
 *
 * whole app needs to handle exceptions and all the status codes properly, just getting the app on its feat first
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/{route}", name="spa-view")
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param $route
     */
    public function indexAction(Request $request, CsrfTokenManagerInterface $csrfTokenManager, $route): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $token = $csrfTokenManager->getToken('authenticate')->getValue();

        return $this->render('index/index.html.twig', ['csrfToken' => $token]);
    }
}