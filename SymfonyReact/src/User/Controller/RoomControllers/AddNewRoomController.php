<?php

namespace App\User\Controller\RoomControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\Builders\RoomDTOBuilder\NewRoomInternalDTOBuilder;
use App\User\Builders\RoomDTOBuilder\RoomResponseDTOBuilder;
use App\User\DTO\RequestDTOs\AddNewRoomRequestDTO;
use App\User\Entity\GroupNames;
use App\User\Exceptions\RoomsExceptions\DuplicateRoomException;
use App\User\Repository\ORM\GroupNameRepositoryInterface;
use App\User\Services\RoomServices\AddNewRoomServiceInterface;
use App\User\Voters\RoomVoter;
use Doctrine\ORM\Exception\ORMException;
use Psr\Log\LoggerInterface;
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

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $elasticLogger)
    {
        $this->logger = $elasticLogger;
    }

    #[Route('add', name:'add-new-room', methods: [Request::METHOD_POST])]
    public function addNewRoom(
        Request $request,
        AddNewRoomServiceInterface $addNewRoomService,
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

        $addNewRoomDTO = NewRoomInternalDTOBuilder::buildInternalNewRoomDTO(
            $addNewRoomRequestDTO->getRoomName(),
        );

        try {
            $addNewRoomService->preProcessNewRoomValues($addNewRoomDTO);
        } catch (DuplicateRoomException $exception) {
            return $this->sendBadRequestJsonResponse([$exception->getMessage()]);
        } catch (ORMException) {
            $this->logger->error('Error occurred while adding new room', ['user' => $this->getUser()?->getUserIdentifier()]);

            return $this->sendBadRequestJsonResponse(['Failed to process room request']);
        }

        $validationErrors = $addNewRoomService->createNewRoom($addNewRoomDTO);
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
            $this->logger->error('Error occurred while adding new room', ['user' => $this->getUser()?->getUserIdentifier()]);

            return $this->sendInternalServerErrorJsonResponse();
        }

        $newRoomResponseDTO = RoomResponseDTOBuilder::buildRoomResponseDTO($newRoom);
        try {
            $normalizedResponse = $this->normalizeResponse($newRoomResponseDTO);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse(['Request successful but failed to normalize response']);
        }
        $this->logger->info('New room added successfully', ['user' => $this->getUser()?->getUserIdentifier()]);

        return $this->sendCreatedResourceJsonResponse($normalizedResponse, 'Room created successfully');
    }
}
