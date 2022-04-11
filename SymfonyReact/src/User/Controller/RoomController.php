<?php

namespace App\User\Controller;

use App\Form\FormMessages;
use App\Traits\API\HomeAppAPIResponseTrait;
use App\User\DTO\RoomDTOs\AddNewRoomDTO;
use App\User\Exceptions\GroupNameExceptions\GroupNameNotFoundException;
use App\User\Services\GroupServices\GroupCheck\GroupCheckServiceInterface;
use App\User\Services\RoomServices\AddNewRoomServiceInterface;
use App\User\Voters\RoomVoter;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/HomeApp/api/user-rooms/')]
class RoomController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route('add-user-room', name:'add-new-room', methods: [Request::METHOD_POST])]
    public function addNewRoom(
        Request $request,
        AddNewRoomServiceInterface $addNewRoomService,
        GroupCheckServiceInterface $groupCheckService,
    ) : Response {

        try {
            $roomNameData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->sendBadRequestJsonResponse(['Request Format not supported']);
        }

        if ($roomNameData['roomName'] === null || $roomNameData['groupId'] === null) {
            return $this->sendBadRequestJsonResponse(['Missing request data']);
        }
        $roomName = $roomNameData['roomName'];
        $groupId = $roomNameData['groupId'];
        $addNewRoomDTO = new AddNewRoomDTO($roomName, $groupId);

        try {
            $groupName = $groupCheckService->checkForGroupById($groupId);
        } catch (GroupNameNotFoundException $exception) {
            return $this->sendBadRequestJsonResponse([$exception->getMessage()]);
        }

        $addNewRoomService->processNewRoomRequest($addNewRoomDTO);

        $newRoom = $addNewRoomService->validateAndCreateRoom($addNewRoomDTO, $groupName);

        if (!empty($addNewRoomService->getUserInputErrors())) {
            return $this->sendBadRequestJsonResponse($addNewRoomService->getUserInputErrors());
        }
        if (!empty($addNewRoomService->getServerErrors())) {
            return $this->sendInternalServerErrorJsonResponse();
        }

        try {
            $this->denyAccessUnlessGranted(RoomVoter::ADD_NEW_ROOM, $newRoom);
        } catch (AccessDeniedException $exception) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($newRoom);
            $em->flush();

            return $this->sendForbiddenAccessJsonResponse([FormMessages::ACCESS_DENIED]);
        }

        return $this->sendCreatedResourceJsonResponse(['Room created successfully']);
    }
}