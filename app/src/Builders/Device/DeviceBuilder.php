<?php

namespace App\Builders\Device;

use App\Entity\Device\Devices;
use App\Entity\User\Group;
use App\Entity\User\Room;
use App\Entity\User\User;
use App\Exceptions\User\GroupExceptions\GroupNotFoundException;
use App\Exceptions\User\RoomsExceptions\RoomNotFoundException;
use App\Repository\User\ORM\GroupRepository;
use App\Repository\User\ORM\RoomRepository;
use App\Repository\User\ORM\UserRepository;
use App\Services\Device\DevicePasswordService\DevicePasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

readonly class DeviceBuilder
{
    public function __construct(
        private UserRepository $userRepository,
        private GroupRepository $groupRepository,
        private RoomRepository $roomRepository,
        private DevicePasswordEncoderInterface $devicePasswordEncoder,
    ) {
    }

    public static function buildDeviceStatic(
        string $deviceName,
        User $createdBy,
        Room $room,
        Group $group,
        array $roles = ["ROLE_DEVICE"],
        ?string $ipAddress = null,
        ?string $externalIpAddress = null,
        ?string $secret = null,
        ?string $password = null,
    ): Devices {
        $devices = new Devices();

        $devices->setDeviceName($deviceName);
        $devices->setCreatedBy($createdBy);
        $devices->setRoomObject($room);
        $devices->setGroupObject($group);
        $devices->setRoles($roles);
        $devices->setIpAddress($ipAddress);
        $devices->setExternalIpAddress($externalIpAddress);
        $devices->setPassword($password);
        $devices->setDeviceSecret($secret);

        return $devices;
    }

    public function buildDevice(
        string $deviceName,
        int $createdBy,
        int $roomID,
        int $groupID,
        array $roles = ["ROLE_DEVICE"],
        ?string $ipAddress = null,
        ?string $externalIpAddress = null,
        ?string $secret = null,
        ?string $password = null,
    ): Devices {
        $user = $this->userRepository->find($createdBy);
        if (!$user instanceof User) {
            throw new UserNotFoundException();
        }

        $room = $this->roomRepository->find($roomID);
        if (!$room instanceof Room) {
            throw new RoomNotFoundException();
        }

        $group = $this->groupRepository->find($groupID);
        if (!$group instanceof Group) {
            throw new GroupNotFoundException();
        }

        $device = self::buildDeviceStatic(
            deviceName: $deviceName,
            createdBy: $user,
            room: $room,
            group: $group,
            roles: $roles,
            ipAddress: $ipAddress,
            externalIpAddress: $externalIpAddress,
            secret: $secret,
            password: $password
        );

        $this->devicePasswordEncoder->encodeDevicePassword($device);

        return $device;
    }
}
