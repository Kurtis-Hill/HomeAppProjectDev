<?php

namespace App\Devices\DeviceServices\NewDevice;

use App\Devices\DTO\DeviceDTO;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NewESP8266DeviceValidatorService implements NewDeviceServiceInterface
{
    private DeviceRepositoryInterface $deviceRepository;

    private Security $security;

    private UserPasswordEncoderInterface $passwordEncoder;

    private ValidatorInterface $validator;

    public function __construct(
        DeviceRepositoryInterface $deviceRepository,
        ValidatorInterface $validator,
        Security $security,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->validator = $validator;
        $this->deviceRepository = $deviceRepository;
        $this->security = $security;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function createNewDevice(DeviceDTO $deviceDTO): Devices
    {
        $newDevice = new Devices();
        $newDevice->setDeviceName($deviceDTO->getDeviceName());
        $newDevice->setCreatedBy($deviceDTO->getCreatedBy());
        $newDevice->setGroupNameObject($deviceDTO->getGroupNameId());
        $newDevice->setRoomObject($deviceDTO->getRoomId());

        return $newDevice;
    }

    public function validateNewDevice(Devices $newDevice): array
    {
        $validatorErrors = (array) $this->validator->validate($newDevice)->getIterator();

        try {
            $this->duplicateDeviceCheck($newDevice);
        }
        catch (DuplicateDeviceException $exception) {
            $validatorErrors[] = $exception->getMessage();
        }

        if (empty($validatorErrors)) {
            $devicePasswordHash = $this->createDevicePasswordHash($newDevice);

            $newDevice->setDeviceSecret($devicePasswordHash);
            $newDevice->setCreatedBy($this->security->getUser());
            $newDevice->setRoles([Devices::ROLE]);
        }

        return $validatorErrors;
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

    private function processNewDeviceForm(FormInterface $addNewDeviceForm, Devices $device): void
    {
        $addNewDeviceForm->submit([
            'deviceName' => $device->getDeviceName(),
            'groupNameObject' => $device->getGroupNameID(),
            'roomObject' => $device->getRoomID(),
        ]);

        if ($addNewDeviceForm->isSubmitted() && $addNewDeviceForm->isValid()) {
            $devicePasswordHash = $this->createDevicePasswordHash($device);

            $validFormData = $addNewDeviceForm->getData();
            $validFormData->setDeviceSecret($devicePasswordHash);
            $validFormData->setCreatedBy($this->security->getUser());
            $validFormData->setRoles([Devices::ROLE]);
        }
        else {
            $this->processFormErrors($addNewDeviceForm);
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
