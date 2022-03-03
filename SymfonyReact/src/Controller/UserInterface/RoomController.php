<?php

namespace App\Controller\UserInterface;

use App\User\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/HomeApp/api/user-rooms/')]
class RoomController extends AbstractController
{

    #[Route('getRooms')]
    public function getUserRooms(Request $request)
    {
        $this->getDoctrine()->getRepository(Room::class)->findBy([
            ''
        ]);
    }
}
