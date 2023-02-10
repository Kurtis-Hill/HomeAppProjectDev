<?php

namespace App\UserInterface\Services\NavBar;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Entity\User;
use App\User\Repository\ORM\RoomRepositoryInterface;
use App\UserInterface\Builders\NavBarDTOBuilders\NavBarDTOBuilder;
use App\UserInterface\Builders\NavBarDTOBuilders\NavBarListLinkDTOBuilder;
use App\UserInterface\DTO\Response\NavBar\NavBarResponseDTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NavBarDataProvider implements NavBarDataProviderInterface
{
    private RoomRepositoryInterface $roomRepository;

    private DeviceRepositoryInterface $deviceRepository;


    private NavBarDTOBuilder $navBarDTOBuilder;

    private array $errors = [];

    public function __construct(
        RoomRepositoryInterface $roomRepository,
        DeviceRepositoryInterface $deviceRepository,
        NavBarDTOBuilder $navBarDTOBuilder,
    ) {
        $this->roomRepository = $roomRepository;
        $this->deviceRepository = $deviceRepository;
        $this->navBarDTOBuilder = $navBarDTOBuilder;
    }

    #[ArrayShape([NavBarResponseDTO::class])]
    public function getNavBarData(User $user): array
    {
        try {
            $userDevices = $this->getDeviceData($user);
        } catch (ORMException) {
            $this->errors[] = [sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Devices')];
        }
        $navbarResponseDTOs[] = $this->getDevicesNavBarResponseObjects($userDevices ?? []);

        $userGroups = $user->getGroupNameMappings();
        $navbarResponseDTOs[] = $this->getGroupNameNavBarResponseObjects($userGroups);

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
        /** @var GroupNames $group */
        foreach ($userGroups as $group) {
            $userGroupNavbarListLinkResponseDTO[] = NavBarListLinkDTOBuilder::buildNavBarListLinkDTO(
                $group->getGroupName(),
                sprintf(
                    '%s%s?%s',
                    CommonURL::HOMEAPP_WEBAPP_URL_BASE,
                    'group',
                    http_build_query(
                        ['group' => $group->getGroupNameID()]
                    )
                )
            );
        }

        return $this->navBarDTOBuilder->buildNavBarResponseDTO(
           'View Groups',
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
                    '%s%s?%s',
                    CommonURL::HOMEAPP_WEBAPP_URL_BASE,
                    'device',
                    http_build_query(
                        ['device-id' => $device->getDeviceID()]
                    )
                )
            );
        }

        return $this->navBarDTOBuilder->buildNavBarResponseDTO(
            'View Devices',
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
                    '%s%s?%s',
                    CommonURL::HOMEAPP_WEBAPP_URL_BASE,
                    'room',
                    http_build_query(
                        ['room-id' => $room->getRoomID()]
                    )
                )
            );
        }

        return $this->navBarDTOBuilder->buildNavBarResponseDTO(
            'View Rooms',
            'person-booth',
            'rooms',
            $userGroupNavbarListLinkResponseDTO ?? [],
        );
    }

    public function getNavbarRequestErrors(): array
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

    /**
     * @throws ORMException
     */
    private function getDeviceData(User $user): array
    {
        return $this->deviceRepository->getAllUsersDevicesByGroupId($user->getAssociatedGroupNameAndIds(), AbstractQuery::HYDRATE_OBJECT);
    }
}
