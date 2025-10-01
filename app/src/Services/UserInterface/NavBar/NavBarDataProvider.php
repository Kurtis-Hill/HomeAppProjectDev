<?php

namespace App\Services\UserInterface\NavBar;

use App\Builders\UserInterface\NavBarDTOBuilders\NavBarDTOBuilder;
use App\Builders\UserInterface\NavBarDTOBuilders\NavBarListLinkDTOBuilder;
use App\DTOs\UserInterface\Response\NavBar\NavBarResponseDTO;
use App\Entity\Device\Devices;
use App\Entity\User\Group;
use App\Entity\User\Room;
use App\Entity\User\User;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Repository\User\ORM\RoomRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\User\GroupServices\UserGroupsFinder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Log\LoggerInterface;

class NavBarDataProvider implements NavBarDataProviderInterface
{
    private RoomRepositoryInterface $roomRepository;

    private DeviceRepositoryInterface $deviceRepository;

    private NavBarDTOBuilder $navBarDTOBuilder;

    private UserGroupsFinder $getGroupNamesFinder;

    private LoggerInterface $elasticLogger;

    public function __construct(
        RoomRepositoryInterface $roomRepository,
        DeviceRepositoryInterface $deviceRepository,
        NavBarDTOBuilder $navBarDTOBuilder,
        UserGroupsFinder $getGroupNamesHandler,
        LoggerInterface $elasticLogger,
    ) {
        $this->roomRepository = $roomRepository;
        $this->deviceRepository = $deviceRepository;
        $this->navBarDTOBuilder = $navBarDTOBuilder;
        $this->getGroupNamesFinder = $getGroupNamesHandler;
        $this->elasticLogger = $elasticLogger;
    }

    #[ArrayShape([NavBarResponseDTO::class])]
    public function getNavBarData(User $user): array
    {
        try {
            $userDevices = $this->deviceRepository->findAllUsersDevicesByGroupId($this->getGroupNamesFinder->getUsersGroups($user), AbstractQuery::HYDRATE_OBJECT);
            $navbarResponseDTOs[] = $this->getDevicesNavBarResponseObjects($userDevices);
        } catch (ORMException) {
            $this->elasticLogger->error(sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Devices'));
        }

        try {
            $userGroups = $this->getGroupNamesFinder->getUsersGroups($user);
            $navbarResponseDTOs[] = $this->getGroupNameNavBarResponseObjects($userGroups);
        } catch (ORMException) {
            $this->elasticLogger->error(sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Groups'));
        }

        try {
            $userRooms = $this->roomRepository->getAllUserRoomsByGroupId($this->getGroupNamesFinder->getUsersGroups($user), AbstractQuery::HYDRATE_OBJECT);
        } catch (ORMException) {
            $this->elasticLogger->error(sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Rooms'));
        }
        $navbarResponseDTOs[] = $this->getRoomNavBarResponseObjects($userRooms ?? []);


        return $navbarResponseDTOs;
    }

    private function getGroupNameNavBarResponseObjects(array $userGroups): NavBarResponseDTO
    {
        /** @var Group $group */
        foreach ($userGroups as $group) {
            $userGroupNavbarListLinkResponseDTO[] = NavBarListLinkDTOBuilder::buildNavBarListLinkDTO(
                $group->getGroupName(),
                sprintf(
                    '%s%s/%d',
                    '/WebApp/',
                    'group',
                    $group->getGroupID(),
                )
            );
        }

        return $this->navBarDTOBuilder->buildNavBarResponseDTO(
            'Groups',
            'users',
            'groups',
            $userGroupNavbarListLinkResponseDTO ?? [],
        );
    }

    public function getDevicesNavBarResponseObjects(array $devices = []): NavBarResponseDTO
    {
        /** @var Devices $device */
        foreach ($devices as $device) {
            $userGroupNavbarListLinkResponseDTO[] = NavBarListLinkDTOBuilder::buildNavBarListLinkDTO(
                $device->getDeviceName(),
                sprintf(
                    '%s%s/%d',
                    '/WebApp/',
                    'devices',
                    $device->getDeviceID()
                )
            );
        }

        return $this->navBarDTOBuilder->buildNavBarResponseDTO(
            'Devices',
            'microchip',
            'devices',
            $userGroupNavbarListLinkResponseDTO ?? [],
        );
    }

    public function getRoomNavBarResponseObjects(array $rooms = []): NavBarResponseDTO
    {
        /** @var Room $room */
        foreach ($rooms as $room) {
            $userGroupNavbarListLinkResponseDTO[] = NavBarListLinkDTOBuilder::buildNavBarListLinkDTO(
                $room->getRoom(),
                sprintf(
                    '%s%s/%d',
                    '/WebApp/',
                    'room',
                    $room->getRoomID(),
                )
            );
        }

        return $this->navBarDTOBuilder->buildNavBarResponseDTO(
            'Rooms',
            'person-booth',
            'rooms',
            $userGroupNavbarListLinkResponseDTO ?? [],
        );
    }
}
