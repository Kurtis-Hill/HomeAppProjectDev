<?php

namespace App\Devices\Builders\DeviceUpdate;

use App\Devices\DTO\Internal\NewDeviceDTO;
use App\Devices\DTO\Internal\UpdateDeviceDTO;
use App\Devices\DTO\Request\DeviceUpdateRequestDTO;
use App\Devices\DTO\Request\NewDeviceRequestDTO;
use App\Devices\Entity\Devices;
use App\User\Entity\Group;
use App\User\Entity\Room;
use App\User\Entity\User;
use App\User\Exceptions\GroupExceptions\GroupNotFoundException;
use App\User\Exceptions\RoomsExceptions\RoomNotFoundException;
use App\User\Repository\ORM\GroupRepositoryInterface;
use App\User\Repository\ORM\RoomRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;

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

    public static function buildNewDeviceDTO(
        User $user,
        Group $groupNameObject,
        Room $roomObject,
        string $deviceName,
        string $devicePassword,
        ?string $deviceIP = null,
    ): NewDeviceDTO {
        $device = new Devices();

        return new NewDeviceDTO(
            $user,
            $groupNameObject,
            $roomObject,
            $deviceName,
            $devicePassword,
            $device,
            $deviceIP,
        );
    }

    /**
     * @throws RoomNotFoundException
     * @throws GroupNotFoundException
     * @throws ORMException
     */
    public function buildNewDeviceDTOFromNewDeviceRequest(NewDeviceRequestDTO $newDeviceRequestDTO, User $createdByUser): NewDeviceDTO
    {
        $groupObject = $this->groupRepository->find($newDeviceRequestDTO->getDeviceGroup());
        if (!$groupObject instanceof Group) {
            throw new GroupNotFoundException(sprintf(GroupNotFoundException::MESSAGE, $newDeviceRequestDTO->getDeviceGroup()));
        }

        $roomObject = $this->roomRepository->find($newDeviceRequestDTO->getDeviceRoom());
        if (!$roomObject instanceof Room) {
            throw new RoomNotFoundException(sprintf(RoomNotFoundException::MESSAGE_WITH_ID, $newDeviceRequestDTO->getDeviceRoom()));
        }

        return self::buildNewDeviceDTO(
            $createdByUser,
            $groupObject,
            $roomObject,
            $newDeviceRequestDTO->getDeviceName(),
            $newDeviceRequestDTO->getDevicePassword(),
            $newDeviceRequestDTO->getDeviceIPAddress(),
        );
    }
}
