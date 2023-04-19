<?php

namespace App\Devices\DeviceServices\UpdateDevice;

use App\Common\API\APIErrorMessages;
use App\Devices\Builders\DeviceUpdate\DeviceDTOBuilder;
use App\Devices\DeviceServices\AbstractESPDeviceService;
use App\Devices\DTO\Internal\UpdateDeviceDTO;
use App\Devices\DTO\Request\DeviceUpdateRequestDTO;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Entity\User;
use App\User\Exceptions\GroupNameExceptions\GroupNameNotFoundException;
use App\User\Exceptions\RoomsExceptions\RoomNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

class UpdateDeviceHandler extends AbstractESPDeviceService implements UpdateDeviceHandlerInterface
{
    /**
     * @throws GroupNameNotFoundException
     * @throws RoomNotFoundException
     * @throws ORMException
     */
    public function buildUpdateDeviceDTO(
        DeviceUpdateRequestDTO $deviceUpdateRequestDTO,
        User $createdByUser,
        Devices $deviceToUpdate,
    ): UpdateDeviceDTO {
        if (!empty($deviceUpdateRequestDTO->getDeviceRoom())) {
            $room = $this->roomRepository->find($deviceUpdateRequestDTO->getDeviceRoom());

            if (!$room instanceof Room) {
                throw new RoomNotFoundException(sprintf(RoomNotFoundException::MESSAGE_WITH_ID, $deviceUpdateRequestDTO->getDeviceRoom()));
            }
        }
        if (!empty($deviceUpdateRequestDTO->getDeviceGroup())) {
            $groupName = $this->groupRepository->find($deviceUpdateRequestDTO->getDeviceGroup());

            if (!$groupName instanceof GroupNames) {
                throw new GroupNameNotFoundException(sprintf(GroupNameNotFoundException::MESSAGE, $deviceUpdateRequestDTO->getDeviceGroup()));
            }
        }

        return DeviceDTOBuilder::buildUpdateDeviceInternalDTO(
            $deviceUpdateRequestDTO,
            $deviceToUpdate,
            $room ?? null,
            $groupName ?? null
        );
    }

    #[ArrayShape(['validationErrors'])]
    public function updateDevice(UpdateDeviceDTO $deviceUpdateRequestDTO): array
    {
        $deviceToUpdate = $deviceUpdateRequestDTO->getDeviceToUpdate();

        if ($deviceUpdateRequestDTO->getProposedGroupNameToUpdateTo() !== null) {
            $deviceToUpdate->setGroupObject($deviceUpdateRequestDTO->getProposedGroupNameToUpdateTo());
        }
        if ($deviceUpdateRequestDTO->getDeviceUpdateRequestDTO()->getDeviceName() !== null) {
            $deviceToUpdate->setDeviceName($deviceUpdateRequestDTO->getDeviceUpdateRequestDTO()->getDeviceName());
        }
        if ($deviceUpdateRequestDTO->getProposedUpdatedRoom() !== null) {
            $deviceToUpdate->setRoomObject($deviceUpdateRequestDTO->getProposedUpdatedRoom());
        }
        if ($deviceUpdateRequestDTO->getDeviceUpdateRequestDTO()->getPassword() !== null) {
            $deviceToUpdate->setDeviceSecret(
                $deviceUpdateRequestDTO->getDeviceUpdateRequestDTO()->getPassword()
            );
            $this->devicePasswordEncoder->encodeDevicePassword($deviceToUpdate);
        }

        return $this->validateUpdatedDevice($deviceToUpdate);
    }

    #[ArrayShape(['validationErrors'])]
    private function validateUpdatedDevice(Devices $deviceToUpdate): array
    {
        try {
            $this->duplicateDeviceCheck($deviceToUpdate->getDeviceName(), $deviceToUpdate->getRoomObject()->getRoomID());
        } catch (DuplicateDeviceException $e) {
            $errors[] = $e->getMessage();
        } catch (ORMException) {
            $error =  sprintf(APIErrorMessages::QUERY_FAILURE, 'Device');
            $this->logger->error($error, ['device' => $deviceToUpdate->getUserIdentifier()]);
            $errors[] = $error;
        }

        $validationConstraintList = $this->validator->validate($deviceToUpdate);
        if (isset($errors) && $this->checkIfErrorsArePresent($validationConstraintList)) {
            return array_merge($errors, $this->getValidationErrorAsArray($validationConstraintList));
        }

        return $this->getValidationErrorAsArray($validationConstraintList);
    }
}
