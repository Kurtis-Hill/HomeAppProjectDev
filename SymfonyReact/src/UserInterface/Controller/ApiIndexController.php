<?php

namespace App\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/HomeApp/api', name: 'home')]
class ApiIndexController extends AbstractController
{
    #[Route('/ping', name: 'ping', methods: [Request::METHOD_GET])]
    public function pingAction(): JsonResponse
    {
        return new JsonResponse('pong', Response::HTTP_OK);
    }
}
