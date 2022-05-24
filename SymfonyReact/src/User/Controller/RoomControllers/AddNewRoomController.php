<?php

namespace App\User\Controller\RoomControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Traits\ValidatorProcessorTrait;
use App\User\Builders\RoomDTOBuilder\NewRoomInternalDTOBuilder;
use App\User\Builders\RoomDTOBuilder\RoomResponseDTOBuilder;
use App\User\DTO\RequestDTOs\AddNewRoomRequestDTO;
use App\User\Entity\GroupNames;
use App\User\Exceptions\RoomsExceptions\DuplicateRoomException;
use App\User\Repository\ORM\GroupNameRepositoryInterface;
use App\User\Services\RoomServices\AddNewRoomServiceInterface;
use App\User\Voters\RoomVoter;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
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
        GroupNameRepositoryInterface $groupNameRepository,
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

        $groupName = $groupNameRepository->findOneById($addNewRoomRequestDTO->getGroupNameID());
        if (!$groupName instanceof GroupNames) {
            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::OBJECT_NOT_FOUND_FOR_ID, 'Groupname', $addNewRoomRequestDTO->getGroupNameID())]);
        }

        $addNewRoomDTO = NewRoomInternalDTOBuilder::buildInternalNewRoomDTO(
            $addNewRoomRequestDTO->getRoomName(),
            $groupName,
        );

        try {
            $addNewRoomService->preProcessNewRoomValues($addNewRoomDTO);
        } catch (DuplicateRoomException $exception) {
            return $this->sendBadRequestJsonResponse([$exception->getMessage()]);
        } catch (ORMException) {
            return $this->sendBadRequestJsonResponse(['Failed to process room request']);
        }

        $validationErrors = $addNewRoomService->createNewRoom($addNewRoomDTO, $groupName);
        $newRoom = $addNewRoomDTO->getNewRoom();
        try {
            $this->denyAccessUnlessGranted(RoomVoter::ADD_NEW_ROOM, $newRoom);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        if (!empty($validationErrors)) {
            return $this->sendBadRequestJsonResponse($validationErrors);
        }

        try {
            $addNewRoomService->saveNewRoom($newRoom);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse();
        }

        $newRoomResponseDTO = RoomResponseDTOBuilder::buildRoomResponseDTO($newRoom);
        try {
            $normalizedResponse = $this->normalizeResponse($newRoomResponseDTO);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse(['Request successful but failed to normalize response']);
        }

        return $this->sendCreatedResourceJsonResponse($normalizedResponse, 'Room created successfully');
    }
}
