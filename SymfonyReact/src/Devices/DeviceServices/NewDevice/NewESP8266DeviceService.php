<?php

namespace App\Devices\DeviceServices\NewDevice;

use App\Devices\DeviceServices\AbstractESPDeviceService;
use App\Devices\DTO\Internal\NewDeviceDTO;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DeviceCreationFailureException;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\User\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

class NewESP8266DeviceService extends AbstractESPDeviceService implements NewDeviceServiceInterface
{
    #[ArrayShape(['validationErrors'])]
    public function processNewDevice(NewDeviceDTO $deviceDTO): array
    {
        $deviceUser = $deviceDTO->getCreatedByUserObject();
        if (!$deviceUser instanceof User) {
            throw new DeviceCreationFailureException(
                DeviceCreationFailureException::DEVICE_FAILED_TO_CREATE
            );
        }

        $newDevice = $deviceDTO->getNewDevice();
        $newDevice->setDeviceName($deviceDTO->getDeviceName());
        $newDevice->setCreatedBy($deviceUser);
        $newDevice->setGroupNameObject($deviceDTO->getGroupNameObject());
        $newDevice->setRoomObject($deviceDTO->getRoomObject());
        $newDevice->setDeviceSecret($this->createDevicePasswordHash($newDevice));
        $this->devicePasswordEncoder->encodeDevicePassword($newDevice);

        return $this->validateNewDevice($newDevice);
    }

    #[ArrayShape(["valdationErrors"])]
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
            $userErrors[] = "device check query failed";
        }

        if (empty($userErrors)) {
            $devicePasswordHash = $this->createDevicePasswordHash($newDevice);

            $newDevice->setDeviceSecret($devicePasswordHash);
            $newDevice->setRoles([Devices::ROLE]);
        }

        return $userErrors ?? [];
    }

    private function createDevicePasswordHash(Devices $device): string
    {
        $secret = $device->getDeviceName();
        $secret .= time();

        return hash("md5", $secret);
    }
}
