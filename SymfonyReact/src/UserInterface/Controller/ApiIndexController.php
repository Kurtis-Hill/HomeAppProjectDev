<?php

namespace App\UserInterface\Controller;

use App\Common\API\Traits\HomeAppAPITrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
