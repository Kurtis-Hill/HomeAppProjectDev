<?php

namespace App\Devices\DeviceServices;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\Builders\Request\DeviceSettingsUpdateEventDTOBuilder;
use App\Devices\DeviceServices\DevicePasswordService\DevicePasswordEncoderInterface;
use App\Devices\Entity\Devices;
use App\Devices\Events\DeviceUpdateEvent;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\User\Repository\ORM\GroupRepositoryInterface;
use App\User\Repository\ORM\RoomRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractESPDeviceService
{
    use ValidatorProcessorTrait;

    protected ValidatorInterface $validator;

    protected DeviceRepositoryInterface $deviceRepository;

    protected DevicePasswordEncoderInterface $devicePasswordEncoder;

    protected GroupRepositoryInterface $groupRepository;

    protected RoomRepositoryInterface $roomRepository;

    private DeviceSettingsUpdateEventDTOBuilder $deviceSettingsUpdateEventDTOBuilder;

    protected EventDispatcher $eventDispatcher;

    protected LoggerInterface $logger;

    public function __construct(
        DeviceRepositoryInterface $deviceRepository,
        ValidatorInterface $validator,
        DevicePasswordEncoderInterface $devicePasswordEncoder,
        GroupRepositoryInterface $groupNameRepository,
        RoomRepositoryInterface $roomRepository,
        DeviceSettingsUpdateEventDTOBuilder $deviceSettingsUpdateEventDTOBuilder,
        LoggerInterface $elasticLogger,
    ) {
        $this->validator = $validator;
        $this->deviceRepository = $deviceRepository;
        $this->devicePasswordEncoder = $devicePasswordEncoder;
        $this->groupRepository = $groupNameRepository;
        $this->roomRepository = $roomRepository;
        $this->deviceSettingsUpdateEventDTOBuilder = $deviceSettingsUpdateEventDTOBuilder;
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

    protected function sendDeviceSettingsUpdateEvent(Devices $device, ?string $plainPassword = null): void
    {
        $updateDeviceSettingsEventDTO = $this->deviceSettingsUpdateEventDTOBuilder->buildDeviceSettingUpdateEventDTO(
            $device->getDeviceName(),
            $plainPassword ?? $device->getDeviceSecret(),
        );

        $this->eventDispatcher->dispatch($updateDeviceSettingsEventDTO, DeviceUpdateEvent::NAME);
    }
}
