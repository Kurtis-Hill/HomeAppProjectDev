<?php

namespace App\UserInterface\Services\UserData;

use App\Common\API\APIErrorMessages;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupRepository;
use App\User\Repository\ORM\RoomRepositoryInterface;
use App\User\Services\GroupServices\UserGroupsFinder;
use App\UserInterface\Builders\UserData\UserDataDTOBuilder;
use App\UserInterface\DTO\Response\UserData\UserDataResponseDTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

class UserDataProvider
{
    private RoomRepositoryInterface $roomRepository;

    private GroupRepository $groupNameRepository;

    private UserGroupsFinder $getGroupNamesHandler;

    private array $errors = [];

    public function __construct(
        RoomRepositoryInterface $roomRepository,
        GroupRepository $groupNameRepository,
        UserGroupsFinder $getGroupNamesHandler,
    ) {
        $this->roomRepository = $roomRepository;
        $this->groupNameRepository = $groupNameRepository;
        $this->getGroupNamesHandler = $getGroupNamesHandler;
    }

    #[ArrayShape([UserDataResponseDTO::class])]
    public function getGeneralUserData(User $user): UserDataResponseDTO
    {
        $userGroups = $this->getGroupNameData($user);
        try {
            $userRooms = $this->getRoomData();
        } catch (ORMException) {
            $userRooms[] = sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Rooms');
            $this->errors[] = 'Failed to get Rooms';
        }

        return UserDataDTOBuilder::buildUserDataDTOBuilder(
            $userRooms,
            $userGroups,
        );
    }

    public function getProcessErrors(): array
    {
        return $this->errors;
    }

    /**
     * @throws ORMException
     */
    private function getRoomData(): array
    {
        return $this->roomRepository->findAll();
    }

    private function getGroupNameData(User $user): array
    {
        return $user->isAdmin()
            ? $this->groupNameRepository->findAll()
            : $user->getAssociatedGroups();
    }
}
