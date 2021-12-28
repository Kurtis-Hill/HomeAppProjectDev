<?php

namespace App\User\Controller;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPIResponseTrait;
use App\Form\FormMessages;
use App\User\DTO\RoomDTOs\AddNewRoomDTO;
use App\User\Entity\Room;
use App\User\Exceptions\GroupNameExceptions\GroupNameNotFoundException;
use App\User\Exceptions\RoomsExceptions\DuplicateRoomException;
use App\User\Services\GroupServices\GroupCheck\GroupCheckServiceInterface;
use App\User\Services\RoomServices\AddNewRoomServiceInterface;
use App\User\Voters\RoomVoter;
use Doctrine\ORM\ORMException;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-rooms/')]
class AddNewRoomController extends AbstractController
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

        try {
            $addNewRoomService->processNewRoomRequest($addNewRoomDTO);
        } catch (DuplicateRoomException $exception) {
            return $this->sendBadRequestJsonResponse([$exception->getMessage()]);
        } catch (ORMException) {
            return $this->sendBadRequestJsonResponse(['Failed to process room request']);
        } catch (GroupNameNotFoundException $exception) {
            return $this->sendBadRequestJsonResponse([$exception->getMessage()]);
        }

        $newRoom = $addNewRoomService->createNewRoom($addNewRoomDTO, $groupName);

        try {
            $this->denyAccessUnlessGranted(RoomVoter::ADD_NEW_ROOM, $newRoom);
        } catch (AccessDeniedException $exception) {
            return $this->sendForbiddenAccessJsonResponse([FormMessages::ACCESS_DENIED]);
        }

        $validationErrors = $addNewRoomService->validateNewRoom($newRoom);

        if (!empty($validationErrors)) {
            return $this->sendBadRequestJsonResponse($validationErrors);
        }

        try {
            $addNewRoomService->saveNewRoom($newRoom);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse();
        }

        return $this->sendCreatedResourceJsonResponse(['Room created successfully']);
    }
}
