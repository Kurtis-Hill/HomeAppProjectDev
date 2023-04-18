<?php

namespace App\UserInterface\Controller;

use App\User\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/HomeApp/WebApp', name: 'home')]
class IndexController extends AbstractController
{
    #[Route('/{route}/{routeTwo}', name: 'spa-view', methods: [Request::METHOD_GET])]
    public function indexAction(Request $request, string $route): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->createAccessDeniedException('Access Denied');
        }

        return $this->render('index/index.html.twig');
    }
}
