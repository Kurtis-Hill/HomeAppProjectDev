<?php

namespace App\UserInterface\Services\NavBar;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DeviceQueryException;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\User\Entity\Group;
use App\User\Entity\Room;
use App\User\Entity\User;
use App\User\Repository\ORM\RoomRepositoryInterface;
use App\User\Services\GroupServices\UserGroupsFinder;
use App\UserInterface\Builders\NavBarDTOBuilders\NavBarDTOBuilder;
use App\UserInterface\Builders\NavBarDTOBuilders\NavBarListLinkDTOBuilder;
use App\UserInterface\DTO\Response\NavBar\NavBarResponseDTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

class NavBarDataProvider implements NavBarDataProviderInterface
{
    private RoomRepositoryInterface $roomRepository;

    private DeviceRepositoryInterface $deviceRepository;


    private NavBarDTOBuilder $navBarDTOBuilder;

    private UserGroupsFinder $getGroupNamesFinder;

    private array $errors = [];

    public function __construct(
        RoomRepositoryInterface $roomRepository,
        DeviceRepositoryInterface $deviceRepository,
        NavBarDTOBuilder $navBarDTOBuilder,
        UserGroupsFinder $getGroupNamesHandler,
    ) {
        $this->roomRepository = $roomRepository;
        $this->deviceRepository = $deviceRepository;
        $this->navBarDTOBuilder = $navBarDTOBuilder;
        $this->getGroupNamesFinder = $getGroupNamesHandler;
    }

    #[ArrayShape([NavBarResponseDTO::class])]
    public function getNavBarData(User $user): array
    {
        try {
            $userDevices = $this->getDeviceData($user);
            $navbarResponseDTOs[] = $this->getDevicesNavBarResponseObjects($userDevices ?? []);
        } catch (ORMException) {
            $this->errors[] = [sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Devices')];
        }

        try {
            $userGroups = $this->getGroupNamesFinder->getGroupNamesForUser($user);
            $navbarResponseDTOs[] = $this->getGroupNameNavBarResponseObjects($userGroups);
        } catch (ORMException) {
            $this->errors[] = [sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Groups')];
        }

        try {
            $userRooms = $this->getRoomData($user);
        } catch (ORMException) {
            $this->errors[] = [sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Rooms')];
        }
        $navbarResponseDTOs[] = $this->getRoomNavBarResponseObjects($userRooms ?? []);


        return $navbarResponseDTOs ?? [];
    }

    private function getGroupNameNavBarResponseObjects(array $userGroups): NavBarResponseDTO
    {
        /** @var Group $group */
        foreach ($userGroups as $group) {
            $userGroupNavbarListLinkResponseDTO[] = NavBarListLinkDTOBuilder::buildNavBarListLinkDTO(
                $group->getGroupName(),
                sprintf(
                    '%s%s/%d',
                    CommonURL::HOMEAPP_WEBAPP_URL_BASE,
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
                    CommonURL::HOMEAPP_WEBAPP_URL_BASE,
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
                    CommonURL::HOMEAPP_WEBAPP_URL_BASE,
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

    #[ArrayShape(['errors'])]
    public function getNavbarRequestErrors(): array
    {
        return $this->errors;
    }

    /**
     * @throws ORMException
     */
    private function getRoomData(User $user): array
    {
        return $this->roomRepository->getAllUserRoomsByGroupId($this->getGroupNamesFinder->getGroupNamesForUser($user), AbstractQuery::HYDRATE_OBJECT);
    }

    /**
     * @throws ORMException
     */
    private function getDeviceData(User $user): array
    {
        return $this->deviceRepository->findAllUsersDevicesByGroupId($this->getGroupNamesFinder->getGroupNamesForUser($user), AbstractQuery::HYDRATE_OBJECT);
    }
}
