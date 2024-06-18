<?php
declare(strict_types=1);

namespace App\Services\Device\UpdateDevice;

use App\DTOs\Device\Internal\UpdateDeviceDTO;
use App\Entity\Device\Devices;
use App\Exceptions\Device\DuplicateDeviceException;
use App\Services\API\APIErrorMessages;
use App\Services\Device\AbstractESPDeviceService;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

class UpdateDeviceHandler extends AbstractESPDeviceService implements UpdateDeviceHandlerInterface
{
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
