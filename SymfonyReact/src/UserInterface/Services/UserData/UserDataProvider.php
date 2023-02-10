<?php

namespace App\UserInterface\Services\UserData;

use App\Common\API\APIErrorMessages;
use App\User\Entity\User;
use App\User\Repository\ORM\RoomRepositoryInterface;
use App\UserInterface\Builders\UserData\UserDataDTOBuilder;
use App\UserInterface\DTO\Response\UserData\UserDataResponseDTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

class UserDataProvider
{
    private RoomRepositoryInterface $roomRepository;

    private array $errors = [];

    public function __construct(
        RoomRepositoryInterface $roomRepository,
    ) {
        $this->roomRepository = $roomRepository;
    }

    #[ArrayShape([UserDataResponseDTO::class])]
    public function getGeneralUserData(User $user): UserDataResponseDTO
    {
        $userGroups = $user->getGroupNameMappings();
        try {
            $userRooms = $this->getRoomData($user);
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
    private function getRoomData(User $user): array
    {
        return $this->roomRepository->getAllUserRoomsByGroupId($user->getAssociatedGroupNameIds(), AbstractQuery::HYDRATE_OBJECT);
    }
}
