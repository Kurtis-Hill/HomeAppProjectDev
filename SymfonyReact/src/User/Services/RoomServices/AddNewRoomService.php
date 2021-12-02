<?php

namespace App\User\Services\RoomServices;

use App\Entity\Core\GroupNames;
use App\User\DTO\RoomDTOs\AddNewRoomDTO;
use App\User\Entity\Room;
use App\User\Exceptions\GroupNameExceptions\GroupNameNotFoundException;
use App\User\Exceptions\RoomsExceptions\DuplicateRoomException;
use App\User\Repository\ORM\GroupNameRepositoryInterface;
use App\User\Repository\ORM\RoomRepository;
use App\User\Repository\ORM\RoomRepositoryInterface;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddNewRoomService implements AddNewRoomServiceInterface
{
    private RoomRepository $roomRepository;

    private GroupNameRepositoryInterface $groupNameRepository;

    private ValidatorInterface $validator;


    private array $userInputErrors;

    private array $serverErrors;

    public function __construct(RoomRepositoryInterface $roomRepository, GroupNameRepositoryInterface $groupNameRepository, ValidatorInterface $validator)
    {
        $this->roomRepository = $roomRepository;
        $this->groupNameRepository = $groupNameRepository;
        $this->validator = $validator;
    }

    public function processNewRoomRequest(AddNewRoomDTO $addNewRoomDTO): ?Room
    {
        try {
            $this->checkForDuplicates($addNewRoomDTO);
            $groupName = $this->checkForGroupName($addNewRoomDTO);

            $newRoom = $this->createNewRoom($addNewRoomDTO, $groupName);
            $this->validateNewRoom($newRoom);
        } catch (ORMException $e) {
            $this->serverErrors[] = 'Failed to execute queries against the room';
            error_log($e->getMessage());
        } catch (DuplicateRoomException | GroupNameNotFoundException $exception) {
            $this->userInputErrors[] = $exception->getMessage();
        } catch (Exception) {
            $this->serverErrors[] = 'Failed to create new room';
        }

        return null;
    }

    private function checkForGroupName(AddNewRoomDTO $addNewRoomDTO): ?string
    {
        $groupName = $this->groupNameRepository->findOneBy(['name' => $addNewRoomDTO->getGroupName()]);
        if (!$groupName instanceof GroupNames) {
            throw new GroupNameNotFoundException(sprintf(GroupNameNotFoundException::MESSAGE, $addNewRoomDTO->getGroupNameId()));
        }
        return $groupName->getName();
    }

    private function checkForDuplicates(AddNewRoomDTO $addNewRoomDTO): void
    {
        $duplicateCheck = $this->roomRepository->findDuplicateRoom($addNewRoomDTO);

        if ($duplicateCheck instanceof Room) {
            throw new DuplicateRoomException(sprintf(DuplicateRoomException::MESSAGE, $addNewRoomDTO->getRoomName()));
        }
    }

    private function createNewRoom(AddNewRoomDTO $addNewRoomDTO, GroupNames $groupName): Room
    {
        $newRoom = new Room();

        $newRoom->setGroupNameID($groupName);
        $newRoom->setRoom($addNewRoomDTO->getRoomName());
    }

    /**
     * @throws Exception
     */
    private function validateNewRoom(Room $room): void
    {
        $validation = $this->validator->validate($room);

        foreach ($validation->getIterator() as $violation) {
            $this->userInputErrors[] = $violation->getMessage();
        }
    }
}
