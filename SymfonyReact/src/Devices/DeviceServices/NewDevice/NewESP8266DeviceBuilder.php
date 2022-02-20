<?php

namespace App\Devices\DeviceServices\NewDevice;

use App\Common\Traits\ValidatorProcessorTrait;
use App\Devices\DTO\NewDeviceDTO;
use App\Devices\DTO\Request\NewDeviceRequestDTO;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DeviceCreationFailureException;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\User\Entity\User;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NewESP8266DeviceBuilder implements NewDeviceServiceInterface
{
    use ValidatorProcessorTrait;

    private DeviceRepositoryInterface $deviceRepository;

    private UserPasswordEncoderInterface $passwordEncoder;

    private ValidatorInterface $validator;

    public function __construct(
        DeviceRepositoryInterface $deviceRepository,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->validator = $validator;
        $this->deviceRepository = $deviceRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    #[ArrayShape(["errors"])]
    public function validateNewDeviceRequest(NewDeviceRequestDTO $deviceRequestDTO): array
    {
        $errors = $this->validator->validate($deviceRequestDTO);

        return $this->getValidationErrorAsArray($errors);
    }

    public function createNewDevice(NewDeviceDTO $deviceDTO): Devices
    {
        $deviceUser = $deviceDTO->getCreatedByUserObject();
        if (!$deviceUser instanceof User) {
            throw new DeviceCreationFailureException(
                DeviceCreationFailureException::DEVICE_FAILED_TO_CREATE
            );
        }

        $newDevice = new Devices();
        $newDevice->setDeviceName($deviceDTO->getDeviceName());
        $newDevice->setCreatedBy($deviceUser);
        $newDevice->setGroupNameObject($deviceDTO->getGroupNameObject());
        $newDevice->setRoomObject($deviceDTO->getRoomObject());

        return $newDevice;
    }

    public function validateNewDevice(Devices $newDevice): array
    {
        $validatorErrors = $this->validator->validate($newDevice);

        if ($this->checkIfErrorsArePresent($validatorErrors)) {
            $userErrors = $this->getValidationErrorAsArray($validatorErrors);
        }

        try {
            $this->duplicateDeviceCheck($newDevice);
        }
        catch (DuplicateDeviceException $exception) {
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

    /**
     * @throws DuplicateDeviceException
     * @throws ORMException
     */
    private function duplicateDeviceCheck(Devices $deviceData): void
    {
        $currentUserDeviceCheck = $this->deviceRepository->findDuplicateDeviceNewDeviceCheck(
            $deviceData->getDeviceName(),
            $deviceData->getRoomObject()->getRoomId(),
        );

        if ($currentUserDeviceCheck instanceof Devices) {
            throw new DuplicateDeviceException(
                sprintf(
                    DuplicateDeviceException::MESSAGE,
                    $currentUserDeviceCheck->getDeviceName(),
                    $currentUserDeviceCheck->getRoomObject()->getRoom()
                )
            );
        }
    }

    private function createDevicePasswordHash(Devices $device): string
    {
        $secret = $device->getDeviceName();
        $secret .= time();

        return hash("md5", $secret);
    }

    public function encodeAndSaveNewDevice(Devices $newDevice): bool
    {
        $this->encodeDevicePassword($newDevice);
        try {
            $this->deviceRepository->persist($newDevice);
            $this->deviceRepository->flush();

            return true;
        } catch (ORMException) {
            return false;
        }
    }

    private function encodeDevicePassword(Devices $device): void
    {
        $device->setPassword(
            $this->passwordEncoder->encodePassword(
                $device,
                $device->getDeviceSecret()
            )
        );
    }
}
