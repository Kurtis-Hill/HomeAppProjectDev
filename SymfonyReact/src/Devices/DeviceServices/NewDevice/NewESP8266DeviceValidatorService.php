<?php

namespace App\Devices\DeviceServices\NewDevice;

use App\Devices\DTO\NewDeviceDTO;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use Doctrine\ORM\ORMException;
 use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NewESP8266DeviceValidatorService implements NewDeviceServiceInterface
{
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

    public function createNewDevice(NewDeviceDTO $deviceDTO): Devices
    {
        $newDevice = new Devices();
        $newDevice->setDeviceName($deviceDTO->getDeviceName());
        $newDevice->setCreatedBy($deviceDTO->getCreatedByUserObject());
        $newDevice->setGroupNameObject($deviceDTO->getGroupNameObject());
        $newDevice->setRoomObject($deviceDTO->getRoomObject());

        return $newDevice;
    }

    public function validateNewDevice(Devices $newDevice): array
    {
        $userErrors = [];
        $validatorErrors = $this->validator->validate($newDevice);

        foreach ($validatorErrors as $error) {
            $userErrors[] = $error->getMessage();
        }
        try {
            $this->duplicateDeviceCheck($newDevice);
        }
        catch (DuplicateDeviceException $exception) {
            $userErrors[] = $exception->getMessage();
        }

        if (empty($userErrors)) {
            $devicePasswordHash = $this->createDevicePasswordHash($newDevice);

            $newDevice->setDeviceSecret($devicePasswordHash);
            $newDevice->setRoles([Devices::ROLE]);
        }

        return $userErrors;
    }

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
