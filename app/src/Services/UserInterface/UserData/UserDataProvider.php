<?php

namespace App\Services\UserInterface\UserData;

use App\Builders\UserInterface\UserData\UserDataDTOBuilder;
use App\DTOs\UserInterface\Response\UserData\UserDataResponseDTO;
use App\Entity\User\User;
use App\Repository\User\ORM\GroupRepository;
use App\Repository\User\ORM\RoomRepositoryInterface;
use App\Services\API\APIErrorMessages;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

class UserDataProvider
{
    private RoomRepositoryInterface $roomRepository;

    private GroupRepository $groupNameRepository;

    private array $errors = [];

    public function __construct(
        RoomRepositoryInterface $roomRepository,
        GroupRepository $groupNameRepository,
    ) {
        $this->roomRepository = $roomRepository;
        $this->groupNameRepository = $groupNameRepository;
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
