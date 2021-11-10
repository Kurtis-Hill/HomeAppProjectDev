<?php

namespace App\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/HomeApp/api/')]
class UserRoomsController extends AbstractController
{

    #[Route('user-rooms')]
    public function getUserRooms(Request $request): Response
    {

    }
}
