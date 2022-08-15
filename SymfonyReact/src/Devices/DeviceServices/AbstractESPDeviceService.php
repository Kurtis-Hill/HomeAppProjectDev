<?php

namespace App\Devices\DeviceServices;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\DeviceServices\DevicePasswordService\DevicePasswordEncoderInterface;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractESPDeviceService
{
    use ValidatorProcessorTrait;

    protected ValidatorInterface $validator;

    protected DeviceRepositoryInterface $deviceRepository;

    protected DevicePasswordEncoderInterface $devicePasswordEncoder;

    public function __construct(
        DeviceRepositoryInterface $deviceRepository,
        ValidatorInterface $validator,
        DevicePasswordEncoderInterface $devicePasswordEncoder,
    ) {
        $this->validator = $validator;
        $this->deviceRepository = $deviceRepository;
        $this->devicePasswordEncoder = $devicePasswordEncoder;
    }

    /**
     * @throws DuplicateDeviceException
     * @throws ORMException
     */
    protected function duplicateDeviceCheck(string $deviceName, int $roomID): void
    {
        $currentUserDeviceCheck = $this->deviceRepository->findDuplicateDeviceNewDeviceCheck(
            $deviceName,
            $roomID,
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

    public function saveDevice(Devices $device): bool
    {
        try {
            $this->deviceRepository->persist($device);
            $this->deviceRepository->flush();

            return true;
        } catch (ORMException) {
            return false;
        }
    }
}
