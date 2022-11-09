<?php

namespace App\UserInterface\Services\NavBar;

use App\Common\API\APIErrorMessages;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\User\Entity\User;
use App\User\Repository\ORM\RoomRepositoryInterface;
use App\UserInterface\Builders\NavBarDTOBuilders\NavBarDTOBuilder;
use App\UserInterface\DTO\Response\NavBar\NavBarResponseDTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

class NavBarDataProviderFacade implements NavBarDataProviderInterface
{
    private RoomRepositoryInterface $roomRepository;

    private DeviceRepositoryInterface $deviceRepository;

    public function __construct(
        RoomRepositoryInterface $roomRepository,
        DeviceRepositoryInterface $deviceRepository,
    ) {
        $this->roomRepository = $roomRepository;
        $this->deviceRepository = $deviceRepository;
    }

    #[ArrayShape([NavBarResponseDTO::class])]
    public function getNavBarData(User $user): NavBarResponseDTO
    {
        $userGroups = $user->getGroupNameObjects();

        try {
            $userRooms = $this->getRoomData($user);
        } catch (ORMException) {
            $userRooms[] = sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Rooms');
            $errors[] = 'Failed to get Rooms';
        }
        try {
            $userDevices = $this->getDeviceData($user);
        } catch (ORMException) {
            $userDevices[] = sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Devices');
            $errors[] = 'Failed to get Device data';
        }

        return NavBarDTOBuilder::buildNavBarResponseDTO(
            $userRooms,
            $userDevices,
            $userGroups,
//            $errors ?? ['Device query failed']
            $errors ?? []
        );
    }

    /**
     * @throws ORMException
     */
    private function getRoomData(User $user): array
    {
        return $this->roomRepository->getAllUserRoomsByGroupId($user->getGroupNameIDs(), AbstractQuery::HYDRATE_OBJECT);
    }

    /**
     * @throws ORMException
     */
    private function getDeviceData(User $user): array
    {
        return $this->deviceRepository->getAllUsersDevicesByGroupId($user->getGroupNameAndIds(), AbstractQuery::HYDRATE_OBJECT);
    }
}
