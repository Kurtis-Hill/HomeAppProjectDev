<?php

namespace App\User\Services\RoomServices;

use App\API\Traits\ObjectValidatorTrait;
use App\User\Entity\GroupNames;
use App\User\DTO\RoomDTOs\AddNewRoomDTO;
use App\User\Entity\Room;
use App\User\Exceptions\RoomsExceptions\DuplicateRoomException;
use App\User\Repository\ORM\RoomRepositoryInterface;
use App\User\Services\GroupServices\GroupCheck\GroupCheckServiceInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddNewRoomService implements AddNewRoomServiceInterface
{
    use ObjectValidatorTrait;

    private RoomRepositoryInterface $roomRepository;

    private ValidatorInterface $validator;

    public function __construct(
        RoomRepositoryInterface $roomRepository,
        GroupCheckServiceInterface $groupCheckService,
        ValidatorInterface $validator,
    ) {
        $this->roomRepository = $roomRepository;
        $this->validator = $validator;
    }

    public function processNewRoomRequest(AddNewRoomDTO $addNewRoomDTO): void
    {
        $this->checkForRoomDuplicates($addNewRoomDTO);
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
        $validationErrors = $this->validateObjectReturnErrorsArray($this->validator, $newRoom);

        return $validationErrors ?? [];
    }

    public function saveNewRoom(Room $room): void
    {
        $this->roomRepository->persist($room);
        $this->roomRepository->flush();
    }

    /**
     * @throws DuplicateRoomException
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
}
