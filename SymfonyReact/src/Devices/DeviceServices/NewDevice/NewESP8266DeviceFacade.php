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
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Entity\User;
use App\User\Exceptions\GroupNameExceptions\GroupNameNotFoundException;
use App\User\Exceptions\RoomsExceptions\RoomNotFoundException;
use App\User\Repository\ORM\GroupNameRepositoryInterface;
use App\User\Repository\ORM\RoomRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NewESP8266DeviceFacade extends AbstractESPDeviceService implements NewDeviceHandlerInterface
{
    /**
     * @throws GroupNameNotFoundException
     * @throws RoomNotFoundException
     * @throws ORMException
     */
    public function findObjectNeededForNewDevice(NewDeviceRequestDTO $newDeviceRequestDTO, User $createdByUser): NewDeviceDTO
    {
        $groupNameObject = $this->groupNameRepository->findOneById($newDeviceRequestDTO->getDeviceGroup());
        if (!$groupNameObject instanceof GroupNames) {
            throw new GroupNameNotFoundException(sprintf(GroupNameNotFoundException::MESSAGE, $newDeviceRequestDTO->getDeviceGroup()));
        }

        $roomObject = $this->roomRepository->findOneById($newDeviceRequestDTO->getDeviceRoom());
        if (!$roomObject instanceof Room) {
            throw new RoomNotFoundException(sprintf(RoomNotFoundException::MESSAGE_WITH_ID, $newDeviceRequestDTO->getDeviceRoom()));
        }

        return DeviceDTOBuilder::buildNewDeviceDTO(
            $createdByUser,
            $groupNameObject,
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
        $newDevice->setGroupNameObject($newDeviceDTO->getGroupNameObject());
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
            // $devicePasswordHash = $this->createDevicePasswordHash($newDevice);
            $newDevice->setDeviceSecret($newDevice->getPassword());
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
