<?php

declare(strict_types=1);

namespace App\Builders\Device\DeviceUpdate;

use App\DTOs\Device\Internal\NewDeviceDTO;
use App\DTOs\Device\Internal\UpdateDeviceDTO;
use App\DTOs\Device\Request\DeviceUpdateRequestDTO;
use App\DTOs\Device\Request\NewDeviceRequestDTO;
use App\Entity\Device\Devices;
use App\Entity\User\Group;
use App\Entity\User\Room;
use App\Entity\User\User;
use App\Exceptions\User\GroupExceptions\GroupNotFoundException;
use App\Exceptions\User\RoomsExceptions\RoomNotFoundException;
use App\Repository\User\ORM\GroupRepositoryInterface;
use App\Repository\User\ORM\RoomRepositoryInterface;

readonly class DeviceDTOBuilder
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository,
        private GroupRepositoryInterface $groupRepository,
    ) {}

    /**
     * @throws GroupNotFoundException
     * @throws RoomNotFoundException
     */
    public function buildUpdateDeviceInternalDTO(
        DeviceUpdateRequestDTO $deviceUpdateRequestDTO,
        Devices $device,
    ): UpdateDeviceDTO {
        if (!empty($deviceUpdateRequestDTO->getDeviceRoom())) {
            $room = $this->roomRepository->find($deviceUpdateRequestDTO->getDeviceRoom());
            if (!$room instanceof Room) {
                throw new RoomNotFoundException(sprintf(RoomNotFoundException::MESSAGE_WITH_ID, $deviceUpdateRequestDTO->getDeviceRoom()));
            }
        }
        if (!empty($deviceUpdateRequestDTO->getDeviceGroup())) {
            $groupName = $this->groupRepository->find($deviceUpdateRequestDTO->getDeviceGroup());
            if (!$groupName instanceof Group) {
                throw new GroupNotFoundException(sprintf(GroupNotFoundException::MESSAGE, $deviceUpdateRequestDTO->getDeviceGroup()));
            }
        }

        return self::buildUpdateDeviceInternalDTOStatic(
            $deviceUpdateRequestDTO,
            $device,
            $room ?? null,
            $groupName ?? null,
        );
    }

    public static function buildUpdateDeviceInternalDTOStatic(
        DeviceUpdateRequestDTO $deviceUpdateRequestDTO,
        Devices $device,
        ?Room $room,
        ?Group $groupName,
    ): UpdateDeviceDTO {
        return new UpdateDeviceDTO(
            $deviceUpdateRequestDTO,
            $device,
            $room ?? null,
            $groupName ?? null,
        );
    }
}
