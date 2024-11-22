<?php

namespace App\Controller\User\RoomControllers;

use App\Entity\User\Room;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\User\RoomServices\DeleteRoomHandler;
use App\Traits\HomeAppAPITrait;
use App\Voters\RoomVoter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-rooms/')]
class DeleteRoomController extends AbstractController
{
    use HomeAppAPITrait;

    public const DELETED_ROOM_SUCCESSFULLY = 'Deleted room: %d successfully';

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $elasticLogger, RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->logger = $elasticLogger;
    }

    #[Route('{roomID}', name:'delete-new-room', methods: [Request::METHOD_DELETE])]
    public function deleteRoom(Room $roomID, Request $request, DeleteRoomHandler $deleteRoomHandler): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted(RoomVoter::DELETE_ROOM, $roomID);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $deletedRoomID = $roomID->getRoomID();
        $roomDeleted = $deleteRoomHandler->handleDeleteRoom($roomID);

        if ($roomDeleted === false) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN]);
        }

        $this->logger->info(sprintf(self::DELETED_ROOM_SUCCESSFULLY, $deletedRoomID), [
            'roomID' => $deletedRoomID,
            'byUser' => $this->getUser()?->getUserIdentifier(),
        ]);

        return $this->sendSuccessfulJsonResponse([sprintf(self::DELETED_ROOM_SUCCESSFULLY, $deletedRoomID)]);
    }
}
