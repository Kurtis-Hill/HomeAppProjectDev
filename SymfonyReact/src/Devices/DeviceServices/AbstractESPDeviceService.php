<?php

namespace App\Devices\DeviceServices;

use App\Common\Repository\IPLogRepository;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\Builders\DeviceUpdate\DeviceSettingsUpdateDTOBuilder;
use App\Devices\DeviceServices\DevicePasswordService\DevicePasswordEncoderInterface;
use App\Devices\Entity\Devices;
use App\Devices\Events\DeviceUpdateEvent;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\User\Repository\ORM\GroupRepositoryInterface;
use App\User\Repository\ORM\RoomRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractESPDeviceService
{
    use ValidatorProcessorTrait;

    protected ValidatorInterface $validator;

    protected DeviceRepositoryInterface $deviceRepository;

    protected DevicePasswordEncoderInterface $devicePasswordEncoder;

    protected GroupRepositoryInterface $groupRepository;

    protected RoomRepositoryInterface $roomRepository;

    private DeviceSettingsUpdateDTOBuilder $deviceSettingsUpdateEventDTOBuilder;

    private IPLogRepository $ipLogRepository;

    protected EventDispatcherInterface $eventDispatcher;

    protected LoggerInterface $logger;

    public function __construct(
        DeviceRepositoryInterface $deviceRepository,
        ValidatorInterface $validator,
        DevicePasswordEncoderInterface $devicePasswordEncoder,
        GroupRepositoryInterface $groupNameRepository,
        RoomRepositoryInterface $roomRepository,
        DeviceSettingsUpdateDTOBuilder $deviceSettingsUpdateEventDTOBuilder,
        IPLogRepository $ipLogRepository,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $elasticLogger,
    ) {
        $this->validator = $validator;
        $this->deviceRepository = $deviceRepository;
        $this->devicePasswordEncoder = $devicePasswordEncoder;
        $this->groupRepository = $groupNameRepository;
        $this->roomRepository = $roomRepository;
        $this->deviceSettingsUpdateEventDTOBuilder = $deviceSettingsUpdateEventDTOBuilder;
        $this->ipLogRepository = $ipLogRepository;
        $this->eventDispatcher = $eventDispatcher;
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

    public function saveDevice(Devices $device, bool $sendUpdateToDevice = false): bool
    {
        try {
            $this->deviceRepository->persist($device);
            $this->deviceRepository->flush();

            if ($device->getIpAddress()) {
                $this->ipLogRepository->removeIPLogByIPAddress($device->getIpAddress());
            }

            if ($sendUpdateToDevice) {
                $this->sendDeviceSettingsUpdateEvent($device);
            }
            return true;
        } catch (ORMException) {
            return false;
        }
    }

    protected function sendDeviceSettingsUpdateEvent(Devices $device, ?string $plainPassword = null): void
    {
        $updateDeviceSettingsEventDTO = $this->deviceSettingsUpdateEventDTOBuilder->buildDeviceSettingUpdateEventDTO(
            $device->getDeviceID(),
            $device->getDeviceName(),
            $plainPassword ?? $device->getDeviceSecret(),
        );
        $deviceSettingsUpdateEvent = new DeviceUpdateEvent($updateDeviceSettingsEventDTO);

        $this->eventDispatcher->dispatch($deviceSettingsUpdateEvent, DeviceUpdateEvent::NAME);
    }
}
