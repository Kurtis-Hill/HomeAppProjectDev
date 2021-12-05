<?php

namespace App\User\Services\RoomServices;

use App\Entity\Core\GroupNames;
use App\User\DTO\RoomDTOs\AddNewRoomDTO;
use App\User\Entity\Room;
use App\User\Exceptions\RoomsExceptions\DuplicateRoomException;
use App\User\Repository\ORM\RoomRepositoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddNewRoomService implements AddNewRoomServiceInterface
{
    private RoomRepositoryInterface $roomRepository;

    private ValidatorInterface $validator;

    private array $userInputErrors = [];

    private array $serverErrors = [];

    public function __construct(RoomRepositoryInterface $roomRepository, ValidatorInterface $validator)
    {
        $this->roomRepository = $roomRepository;
        $this->validator = $validator;
    }

    public function processNewRoomRequest(AddNewRoomDTO $addNewRoomDTO): ?Room
    {
        try {
            $this->checkForRoomDuplicates($addNewRoomDTO);
        } catch (DuplicateRoomException $exception) {
            $this->userInputErrors[] = $exception->getMessage();
        }

        return null;
    }

    public function validateAndCreateRoom(AddNewRoomDTO $addNewRoomDTO, GroupNames $groupName): ?Room
    {
        $newRoom = $this->createNewRoom($addNewRoomDTO, $groupName);

        $validated = $this->validateNewRoom($newRoom);

        if ($validated) {
            $this->roomRepository->persist($newRoom);
            $this->roomRepository->flush($newRoom);
        }


        return $newRoom;
    }

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

    private function createNewRoom(AddNewRoomDTO $addNewRoomDTO, GroupNames $groupName): Room
    {
        $newRoom = new Room();

        $newRoom->setGroupNameID($groupName);
        $newRoom->setRoom($addNewRoomDTO->getRoomName());

        return $newRoom;
    }

    private function validateNewRoom(Room $room): bool
    {
        $errors = $this->validator->validate($room);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->userInputErrors[] = $error->getMessage();
            }

            return false;
        }

        return true;
    }

    public function getUserInputErrors(): array
    {
        return $this->userInputErrors;
    }

    public function getServerErrors(): array
    {
        return $this->serverErrors;
    }
}
