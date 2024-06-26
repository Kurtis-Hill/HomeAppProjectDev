<?php
declare(strict_types=1);

namespace App\Services\Device\NewDevice;

use App\DTOs\Device\Internal\NewDeviceDTO;
use App\Entity\Device\Devices;
use App\Entity\User\User;
use App\Exceptions\Device\DeviceCreationFailureException;
use App\Exceptions\Device\DuplicateDeviceException;
use App\Services\Device\AbstractESPDeviceService;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

class NewESP8266DeviceHandler extends AbstractESPDeviceService implements NewDeviceHandlerInterface
{
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
        $newDevice->setIpAddress($newDeviceDTO->getDeviceIP());

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
