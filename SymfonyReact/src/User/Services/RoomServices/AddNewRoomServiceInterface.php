<?php

namespace App\User\Services\RoomServices;

use App\User\DTO\Internal\RoomDTOs\AddNewRoomDTO;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Exceptions\GroupNameExceptions\GroupNameNotFoundException;
use App\User\Exceptions\RoomsExceptions\DuplicateRoomException;
use JetBrains\PhpStorm\ArrayShape;
use Doctrine\ORM\Exception\ORMException;

interface AddNewRoomServiceInterface
{
    /**
     * @throws DuplicateRoomException|ORMException
     */
    public function preProcessNewRoomValues(AddNewRoomDTO $addNewRoomDTO): void;

    #[ArrayShape(['validationErrors'])]
    public function createNewRoom(AddNewRoomDTO $addNewRoomDTO): array;

    /**
     * @throws ORMException
     */
    public function saveNewRoom(Room $room): void;
}
