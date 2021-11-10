<?php

namespace App\Rooms\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserRoomsController extends AbstractController
{
    #[Route('/HomeApp/api/user-rooms')]
    public function getUserRooms(Request $request): Response
    {

    }
}
