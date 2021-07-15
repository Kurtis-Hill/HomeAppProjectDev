<?php

namespace App\Controller\UserInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;


#[Route('/HomeApp/WebApp', name: 'home')]
class IndexController extends AbstractController
{
    /**
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param $route
     * @return Response
     */
    #[Route('/{route}', name: 'spa-view', methods: [Request::METHOD_GET])]
    public function indexAction(Request $request, CsrfTokenManagerInterface $csrfTokenManager, $route): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $token = $csrfTokenManager->getToken('authenticate')->getValue();

        return $this->render('index/index.html.twig', ['csrfToken' => $token]);
    }
}
