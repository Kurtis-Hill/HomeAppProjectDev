<?php

namespace App\User\Controller;

use App\Traits\API\HomeAppAPIResponseTrait;
use App\User\DTO\RoomDTOs\AddNewRoomDTO;
use App\User\Services\RoomServices\AddNewRoomServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/HomeApp/api/user-rooms/')]
class RoomController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route('add-user-room')]
    public function addNewRoom(Request $request, AddNewRoomServiceInterface $addNewRoomService): Response
    {
        $roomName = $request->get('roomName');
        $groupId = $request->get('groupId');

        $addNewRoomDTO = new AddNewRoomDTO($roomName, $groupId);

        $newRoom = $addNewRoomService->processNewRoomRequest()
        return $this->sendCreatedResourceJsonResponse();
    }
}
