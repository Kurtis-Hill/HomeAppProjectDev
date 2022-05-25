<?php

namespace App\Devices\DeviceServices\UpdateDevice;

use App\Common\API\APIErrorMessages;
use App\Devices\DeviceServices\AbstractESPDeviceService;
use App\Devices\DeviceServices\DevicePasswordService\DevicePasswordEncoderInterface;
use App\Devices\DTO\Internal\UpdateDeviceDTO;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateDeviceService extends AbstractESPDeviceService implements UpdateDeviceServiceInterface
{
    #[ArrayShape(['errors'])]
    public function updateDevice(UpdateDeviceDTO $deviceUpdateRequestDTO): array
    {
        $deviceToUpdate = $deviceUpdateRequestDTO->getDeviceToUpdate();

        if ($deviceUpdateRequestDTO->getProposedGroupNameToUpdateTo() !== null) {
            $deviceToUpdate->setGroupNameObject($deviceUpdateRequestDTO->getProposedGroupNameToUpdateTo());
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

    private function validateUpdatedDevice(Devices $deviceToUpdate): array
    {
        try {
            $this->duplicateDeviceCheck($deviceToUpdate->getDeviceName(), $deviceToUpdate->getRoomObject()->getRoomID());
        } catch (DuplicateDeviceException $e) {
            $errors[] = $e->getMessage();
        } catch (ORMException) {
            $errors[] = sprintf(APIErrorMessages::QUERY_FAILURE, 'Device');
        }

        $validationConstraintList = $this->validator->validate($deviceToUpdate);
        if (isset($errors) && $this->checkIfErrorsArePresent($validationConstraintList)) {
            return array_merge($errors, $this->getValidationErrorAsArray($validationConstraintList));
        }

        return $this->getValidationErrorAsArray($validationConstraintList);
    }
}
