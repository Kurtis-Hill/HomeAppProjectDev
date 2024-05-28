<?php

namespace App\User\Services\RoomServices;

use App\User\Entity\Room;
use App\User\Repository\ORM\RoomRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;

class DeleteRoomHandler
{
    private RoomRepositoryInterface $roomRepository;

    private LoggerInterface $logger;

    public function __construct(
        RoomRepositoryInterface $roomRepository,
        LoggerInterface $logger,
    ) {
        $this->roomRepository = $roomRepository;
        $this->logger = $logger;
    }

    public function handleDeleteRoom(Room $room): bool
    {
        try {
            $this->roomRepository->remove($room);
            $this->roomRepository->flush();
        } catch (ORMException|OptimisticLockException $e) {
            $this->logger->error('Failed to remove room:' .$room->getRoomID(), [
                'exception' => $e->getMessage(),
                'time' => date('d-M-Y H:i:s')
            ]);
            return false;
        }

        return true;
    }
}
