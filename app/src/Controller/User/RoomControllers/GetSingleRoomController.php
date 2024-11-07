<?php

namespace App\Controller\User\RoomControllers;

use App\Builders\User\RoomDTOBuilder\RoomResponseDTOBuilder;
use App\Entity\User\Room;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestTypeEnum;
use App\Traits\HomeAppAPITrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-rooms/')]
class GetSingleRoomController extends AbstractController
{
    use HomeAppAPITrait;

    #[Route('{room}', name: 'get-single-user-room', methods: [Request::METHOD_GET])]
    public function getSingleRoom(Room $room): JsonResponse
    {
        $roomResponseDTO = RoomResponseDTOBuilder::buildRoomResponseDTO($room);
        try {
            $normalizedResponse = $this->normalize($roomResponseDTO, [RequestTypeEnum::FULL->value]);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
