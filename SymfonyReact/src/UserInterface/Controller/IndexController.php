<?php

namespace App\UserInterface\Controller;

use App\Common\API\Traits\HomeAppAPITrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/HomeApp/WebApp', name: 'home')]
class IndexController extends AbstractController
{
    use HomeAppAPITrait;

    #[Route('/{route}', name: 'spa-view-one', methods: [Request::METHOD_GET])]
    public function indexOneAction(Request $request, string $route): Response
    {
        return $this->render('index/index.html.twig');
    }
//
//    #[Route('/{route}/{routeTwo}', name: 'spa-view', methods: [Request::METHOD_GET])]
//    public function indexAction(Request $request, string $route): Response
//    {
//        return $this->render('index/index.html.twig');
//    }
}
