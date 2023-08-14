<?php

namespace App\Devices\DeviceServices\NewDevice;

use App\Common\API\APIErrorMessages;
use App\Devices\Builders\DeviceUpdate\DeviceDTOBuilder;
use App\Devices\DeviceServices\AbstractESPDeviceService;
use App\Devices\DeviceServices\DevicePasswordService\DevicePasswordEncoderInterface;
use App\Devices\DTO\Internal\NewDeviceDTO;
use App\Devices\DTO\Request\NewDeviceRequestDTO;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DeviceCreationFailureException;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\User\Entity\Group;
use App\User\Entity\Room;
use App\User\Entity\User;
use App\User\Exceptions\GroupExceptions\GroupNotFoundException;
use App\User\Exceptions\RoomsExceptions\RoomNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

class NewESP8266DeviceFacade extends AbstractESPDeviceService implements NewDeviceHandlerInterface
{
    /**
     * @throws GroupNotFoundException
     * @throws RoomNotFoundException
     * @throws ORMException
     */
    public function processAddDeviceObjects(NewDeviceRequestDTO $newDeviceRequestDTO, User $createdByUser): NewDeviceDTO
    {
        $groupObject = $this->groupRepository->find($newDeviceRequestDTO->getDeviceGroup());
        if (!$groupObject instanceof Group) {
            throw new GroupNotFoundException(sprintf(GroupNotFoundException::MESSAGE, $newDeviceRequestDTO->getDeviceGroup()));
        }

        $roomObject = $this->roomRepository->find($newDeviceRequestDTO->getDeviceRoom());
        if (!$roomObject instanceof Room) {
            throw new RoomNotFoundException(sprintf(RoomNotFoundException::MESSAGE_WITH_ID, $newDeviceRequestDTO->getDeviceRoom()));
        }

        return DeviceDTOBuilder::buildNewDeviceDTO(
            $createdByUser,
            $groupObject,
            $roomObject,
            $newDeviceRequestDTO->getDeviceName(),
            $newDeviceRequestDTO->getDevicePassword(),
        );
    }

    #[ArrayShape(['validationErrors'])]
    public function processNewDevice(NewDeviceDTO $newDeviceDTO): array
    {
        $deviceUser = $newDeviceDTO->getCreatedByUserObject();
        if (!$deviceUser instanceof User) {
            $this->logger->error('Device not created by user', ['device' => $deviceUser->getUserIdentifier()]);
            throw new DeviceCreationFailureException(
                DeviceCreationFailureException::DEVICE_FAILED_TO_CREATE
            );
        }

        $newDevice = $newDeviceDTO->getNewDevice();
        $newDevice->setDeviceName($newDeviceDTO->getDeviceName());
        $newDevice->setCreatedBy($deviceUser);
        $newDevice->setGroupObject($newDeviceDTO->getGroupNameObject());
        $newDevice->setRoomObject($newDeviceDTO->getRoomObject());
        $newDevice->setDeviceSecret($newDeviceDTO->getDevicePassword());
        $newDevice->setPassword($newDeviceDTO->getDevicePassword());

        $validationResult = $this->validateNewDevice($newDevice);
        if (empty($validationResult)) {
            $this->devicePasswordEncoder->encodeDevicePassword($newDevice);
        }

        return $validationResult;
    }

    #[ArrayShape(["validationErrors"])]
    private function validateNewDevice(Devices $newDevice): array
    {
        $validatorErrors = $this->validator->validate($newDevice);
        if ($this->checkIfErrorsArePresent($validatorErrors)) {
            $userErrors = $this->getValidationErrorAsArray($validatorErrors);
        }

        try {
            $this->duplicateDeviceCheck(
                $newDevice->getDeviceName(),
                $newDevice->getRoomObject()->getRoomID()
            );
        } catch (DuplicateDeviceException $exception) {
            $userErrors[] = $exception->getMessage();
        } catch (ORMException $e) {
            $this->logger->error($e->getMessage());
            $userErrors[] = "device check query failed";
        }

        if (empty($userErrors)) {
            $newDevice->setDeviceSecret($newDevice->getPassword());
            $newDevice->setRoles([Devices::ROLE]);
        }

        return $userErrors ?? [];
    }
}
