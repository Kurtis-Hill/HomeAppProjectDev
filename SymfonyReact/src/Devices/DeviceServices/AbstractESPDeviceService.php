<?php

namespace App\Devices\DeviceServices;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\DeviceServices\DevicePasswordService\DevicePasswordEncoderInterface;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\User\Repository\ORM\GroupNameRepositoryInterface;
use App\User\Repository\ORM\RoomRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractESPDeviceService
{
    use ValidatorProcessorTrait;

    protected ValidatorInterface $validator;

    protected DeviceRepositoryInterface $deviceRepository;

    protected DevicePasswordEncoderInterface $devicePasswordEncoder;

    protected GroupNameRepositoryInterface $groupNameRepository;

    protected RoomRepositoryInterface $roomRepository;

    protected LoggerInterface $logger;

    public function __construct(
        DeviceRepositoryInterface $deviceRepository,
        ValidatorInterface $validator,
        DevicePasswordEncoderInterface $devicePasswordEncoder,
        GroupNameRepositoryInterface $groupNameRepository,
        RoomRepositoryInterface $roomRepository,
        LoggerInterface $elasticLogger,
    ) {
        $this->validator = $validator;
        $this->deviceRepository = $deviceRepository;
        $this->devicePasswordEncoder = $devicePasswordEncoder;
        $this->groupNameRepository = $groupNameRepository;
        $this->roomRepository = $roomRepository;
        $this->logger = $elasticLogger;
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
