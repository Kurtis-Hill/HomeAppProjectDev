<?php

namespace App\User\Controller;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPITrait;
use App\Common\Traits\ValidatorProcessorTrait;
use App\User\DTO\RequestDTOs\AddNewRoomRequestDTO;
use App\User\DTO\RoomDTOs\AddNewRoomDTO;
use App\User\Exceptions\GroupNameExceptions\GroupNameNotFoundException;
use App\User\Exceptions\RoomsExceptions\DuplicateRoomException;
use App\User\Services\GroupServices\GroupCheck\GroupCheckServiceInterface;
use App\User\Services\RoomServices\AddNewRoomServiceInterface;
use App\User\Voters\RoomVoter;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-rooms/')]
class AddNewRoomController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route('add-user-room', name:'add-new-room', methods: [Request::METHOD_POST])]
    public function addNewRoom(
        Request $request,
        AddNewRoomServiceInterface $addNewRoomService,
        GroupCheckServiceInterface $groupCheckService,
        ValidatorInterface $validator,
    ): Response {
        $addNewRoomRequestDTO = new AddNewRoomRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                AddNewRoomRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $addNewRoomRequestDTO]
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        $validationErrors = $validator->validate($addNewRoomRequestDTO);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors), 'Validation Errors Occurred');
        }

        $addNewRoomDTO = new AddNewRoomDTO(
             $addNewRoomRequestDTO->getRoomName(),
            $addNewRoomRequestDTO->getGroupId(),
        );

        try {
            $groupName = $groupCheckService->checkForGroupById($addNewRoomRequestDTO->getGroupId());
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
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
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
