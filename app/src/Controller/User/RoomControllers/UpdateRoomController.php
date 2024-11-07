<?php

namespace App\Controller\User\RoomControllers;

use App\Builders\User\RoomDTOBuilder\RoomResponseDTOBuilder;
use App\DTOs\User\Request\Room\RoomRequestDTO;
use App\Entity\User\Room;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-rooms/')]
class UpdateRoomController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route('{room}/update', name: 'update-user-room', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function updateRoom(
        #[MapRequestPayload]
        RoomRequestDTO $roomRequestDTO,
        Room $room,
        ValidatorInterface $validator,
        Request $request,
        ManagerRegistry $managerRegistry
    ): JsonResponse {
        try {
            $validationErrors = $validator->validate($roomRequestDTO);
            if ($this->checkIfErrorsArePresent($validationErrors)) {
                return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors));
            }
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $room->setRoom($roomRequestDTO->getRoomName());

        try {
            $validationErrors = $validator->validate($room);
            if ($this->checkIfErrorsArePresent($validationErrors)) {
                return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors));
            }
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $responseType = $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::ONLY->value);
        try {
            $normalizedRoom = $this->normalize(RoomResponseDTOBuilder::buildRoomResponseDTO($room), [$responseType]);
        } catch (Exception $e) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedRoom);
    }
}
