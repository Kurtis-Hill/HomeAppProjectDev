<?php

namespace App\Controller\UserInterface;

use App\Traits\HomeAppAPITrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/HomeApp/api/user', name: 'home')]
class ApiIndexController extends AbstractController
{
    use HomeAppAPITrait;

    #[Route('/ping', name: 'ping', methods: [Request::METHOD_GET])]
    public function pingAction(): JsonResponse
    {
        return new JsonResponse('pong', Response::HTTP_OK);
    }
}
