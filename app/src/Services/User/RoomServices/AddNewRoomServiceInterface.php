<?php

namespace App\Services\User\RoomServices;

use App\DTOs\User\Internal\RoomDTOs\AddNewRoomDTO;
use App\Entity\User\Room;
use App\Exceptions\User\RoomsExceptions\DuplicateRoomException;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

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
