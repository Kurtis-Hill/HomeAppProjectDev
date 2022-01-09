<?php

namespace App\UserInterface\Services\NavBar;

use App\API\APIErrorMessages;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\User\Entity\User;
use App\User\Repository\ORM\RoomRepositoryInterface;
use App\UserInterface\Exceptions\WrongUserTypeException;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Security\Core\User\UserInterface;

class NavBarService implements NavBarServiceInterface
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

    #[ArrayShape(
        [
            'rooms' => [
                'roomID' => 'string',
                'room' => 'string',
            ],
            'devices' => [
                'deviceNameID' => 'string',
                'deviceName' => 'string',
                'groupNameID' => 'string',
                'roomID' => 'string',
            ],
            'groupNames' => [
                'groupNames' => ['string'],
            ],
            'errors' => ['string'],
        ]
    )]
    public function getNavBarData(UserInterface $user): array
    {
        if (!$user instanceof User) {
            throw new WrongUserTypeException(WrongUserTypeException::WRONG_USER_TYPE_MESSAGE);
        }
        $usersGroupNameIds = $user->getGroupNameAndIds();

        try {
            $userRooms = $this->getRoomData($user);
        } catch (ORMException $e) {
            $userRooms[] = sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Rooms');
            $errors[] = 'Rooms query failed';
        }
        try {
            $userDevices = $this->getDeviceData($user);
        } catch (ORMException $e) {
            $userDevices[] = sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Devices');
            $errors[] = 'Device query failed';
        }

        return  [
            'rooms' => $userRooms,
            'devices' => $userDevices,
            'groupNames' => $usersGroupNameIds,
            'errors' => $errors ?? [],
        ];
    }

    /**
     * @throws ORMException
     */
    private function getRoomData(User $user): array
    {
        return $this->roomRepository->getAllUserRoomsByGroupId($user->getGroupNameIDs());
    }

    /**
     * @throws ORMException
     */
    private function getDeviceData(User $user): array
    {
        return $this->deviceRepository->getAllUsersDevicesByGroupId($user->getGroupNameAndIds());
    }
}
