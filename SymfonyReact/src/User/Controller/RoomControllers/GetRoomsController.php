<?php

namespace App\User\Controller\RoomControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Exceptions\ValidatorProcessorException;
use App\Common\Services\PaginationCalculator;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Services\RequestTypeEnum;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\Builders\RoomDTOBuilder\RoomResponseDTOBuilder;
use App\User\Entity\User;
use App\User\Repository\ORM\RoomRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-rooms/', name: 'get-user-rooms')]
class GetRoomsController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    private const MAX_ROOM_RETURN_SIZE = 100;

    private RequestQueryParameterHandler $requestQueryParameterHandler;

    public function __construct(RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->requestQueryParameterHandler = $requestQueryParameterHandler;
    }

    #[Route('all', name: 'get-user-rooms_multiple', methods: [Request::METHOD_GET])]
    public function getAllUserRooms(Request $request, RoomRepositoryInterface $roomRepository): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::FULL->value),
                $request->get('page', 1),
                $request->get('limit', self::MAX_ROOM_RETURN_SIZE),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $rooms = $roomRepository->findAllRoomsPaginatedResult(
            PaginationCalculator::calculateOffset($requestDTO->getLimit(), $requestDTO->getPage()),
            $requestDTO->getLimit(),
        );
        foreach ($rooms as $room) {
            $roomResponseDTO[] = RoomResponseDTOBuilder::buildRoomResponseDTO($room);
        }

        try {
            $normalizedResponse = $this->normalizeResponse($roomResponseDTO ?? [], [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
