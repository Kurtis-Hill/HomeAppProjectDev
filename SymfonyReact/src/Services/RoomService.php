<?php


namespace App\Services;


use App\HomeAppCore\HomeAppRoomAbstract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class RoomService extends HomeAppRoomAbstract
{
    public function __construct(EntityManagerInterface $em, Security $security, Request $request)
    {
        parent::__construct($em, $security);

        $this->currentRoom = $request->get('room');
    }
}