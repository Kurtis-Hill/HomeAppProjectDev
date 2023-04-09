<?php

namespace App\User\Services\RoomServices;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\DTO\InternalDTOs\RoomDTOs\AddNewRoomDTO;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Exceptions\RoomsExceptions\DuplicateRoomException;
use App\User\Repository\ORM\RoomRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddNewRoomHandler implements AddNewRoomServiceInterface
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

    public function preProcessNewRoomValues(AddNewRoomDTO $addNewRoomDTO): void
    {
        $this->checkForRoomDuplicates($addNewRoomDTO);
    }

    /**
     * @throws DuplicateRoomException|ORMException
     */
    private function checkForRoomDuplicates(AddNewRoomDTO $addNewRoomDTO): void
    {
        $duplicateCheck = $this->roomRepository->findRoomByName(
            $addNewRoomDTO->getRoomName(),
        );

        if ($duplicateCheck instanceof Room) {
            throw new DuplicateRoomException(sprintf(DuplicateRoomException::MESSAGE, $addNewRoomDTO->getRoomName()));
        }
    }

    #[ArrayShape(['validationErrors'])]
    public function createNewRoom(AddNewRoomDTO $addNewRoomDTO): array
    {
        $newRoom = $addNewRoomDTO->getNewRoom();

        $newRoom->setRoom($addNewRoomDTO->getRoomName());

        return $this->validateNewRoom($newRoom);
    }

    private function validateNewRoom(Room $newRoom): array
    {
        $validationErrors = $this->validator->validate($newRoom);

        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->getValidationErrorAsArray($validationErrors);
        }

        return [];
    }

    public function saveNewRoom(Room $room): void
    {
        $this->roomRepository->persist($room);
        $this->roomRepository->flush();
    }
}
