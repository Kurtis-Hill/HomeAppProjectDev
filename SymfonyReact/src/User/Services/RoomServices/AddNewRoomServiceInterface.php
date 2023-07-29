<?php

namespace App\User\Services\RoomServices;

use App\User\DTO\InternalDTOs\RoomDTOs\AddNewRoomDTO;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Exceptions\GroupNameExceptions\GroupNameNotFoundException;
use App\User\Exceptions\RoomsExceptions\DuplicateRoomException;
use Doctrine\ORM\ORMException;

interface AddNewRoomServiceInterface
{
    /**
     * @throws DuplicateRoomException | ORMException | GroupNameNotFoundException
     */
    public function processNewRoomRequest(AddNewRoomDTO $addNewRoomDTO): void;

    public function validateNewRoom(Room $newRoom): array;

    public function createNewRoom(AddNewRoomDTO $addNewRoomDTO, GroupNames $groupName): Room;
    /**
     * @throws ORMException
     */
    public function saveNewRoom(Room $room): void;
}
