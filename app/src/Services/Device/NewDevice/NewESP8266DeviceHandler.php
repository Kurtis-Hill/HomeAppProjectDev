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
            $newDevice->setRoles([Devices::ROLE]);
            $newDevice->setDeviceSecret($newDevice->getPassword());
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
            $this->elasticLogger->error($e->getMessage());
            $userErrors[] = "device check query failed";
        }

        return $userErrors ?? [];
    }
}
