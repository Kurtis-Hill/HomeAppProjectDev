<?php

namespace App\User\Services\RoomServices;

use App\Common\Traits\ValidatorProcessorTrait;
use App\User\DTO\InternalDTOs\RoomDTOs\AddNewRoomDTO;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Exceptions\RoomsExceptions\DuplicateRoomException;
use App\User\Repository\ORM\RoomRepositoryInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddNewRoomService implements AddNewRoomServiceInterface
{
    use ValidatorProcessorTrait;

    private RoomRepositoryInterface $roomRepository;

    private ValidatorInterface $validator;

    public function __construct(
        RoomRepositoryInterface $roomRepository,
        ValidatorInterface $validator,
    ) {
        $this->roomRepository = $roomRepository;
        $this->validator = $validator;
    }

    public function processNewRoomRequest(AddNewRoomDTO $addNewRoomDTO): void
    {
        $this->checkForRoomDuplicates($addNewRoomDTO);
    }

    /**
     * @throws DuplicateRoomException|ORMException
     */
    private function checkForRoomDuplicates(AddNewRoomDTO $addNewRoomDTO): void
    {
        $duplicateCheck = $this->roomRepository->findDuplicateRoom(
            $addNewRoomDTO->getRoomName(),
            $addNewRoomDTO->getGroupNameId()
        );

        if ($duplicateCheck instanceof Room) {
            throw new DuplicateRoomException(sprintf(DuplicateRoomException::MESSAGE, $addNewRoomDTO->getRoomName()));
        }
    }

    public function createNewRoom(AddNewRoomDTO $addNewRoomDTO, GroupNames $groupName): Room
    {
        $newRoom = new Room();

        $newRoom->setGroupNameID($groupName);
        $newRoom->setRoom($addNewRoomDTO->getRoomName());

        return $newRoom;
    }

    public function validateNewRoom(Room $newRoom): array
    {
        $validationErrors = $this->validator->validate($newRoom);

        return $this->getValidationErrorAsArray($validationErrors);
    }

    public function saveNewRoom(Room $room): void
    {
        $this->roomRepository->persist($room);
        $this->roomRepository->flush();
    }
}
