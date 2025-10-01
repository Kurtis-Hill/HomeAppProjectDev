<?php
declare(strict_types=1);

namespace App\Services\Device;

use App\Builders\Device\DeviceUpdate\DeviceSettingsUpdateDTOBuilder;
use App\Entity\Device\Devices;
use App\Events\Device\DeviceUpdateEvent;
use App\Exceptions\Device\DuplicateDeviceException;
use App\Repository\Common\ORM\IPLogRepository;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Repository\User\ORM\GroupRepositoryInterface;
use App\Repository\User\ORM\RoomRepositoryInterface;
use App\Services\Device\DevicePasswordService\DevicePasswordEncoderInterface;
use App\Traits\ValidatorProcessorTrait;
use Doctrine\ORM\Exception\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractESPDeviceService
{
    use ValidatorProcessorTrait;

    public function __construct(
        protected DeviceRepositoryInterface $deviceRepository,
        protected ValidatorInterface $validator,
        protected DevicePasswordEncoderInterface $devicePasswordEncoder,
        protected GroupRepositoryInterface $groupRepository,
        protected RoomRepositoryInterface $roomRepository,
        protected DeviceSettingsUpdateDTOBuilder $deviceSettingsUpdateEventDTOBuilder,
        protected IPLogRepository $ipLogRepository,
        protected DuplicateDeviceChecker $duplicateDeviceChecker,
        protected EventDispatcherInterface $eventDispatcher,
        protected LoggerInterface $elasticLogger,
    ) {
    }

    /**
     * @throws DuplicateDeviceException
     * @throws ORMException
     */
    protected function duplicateDeviceCheck(string $deviceName, int $roomID): void
    {
        $currentUserDeviceCheck = $this->duplicateDeviceChecker->duplicateDeviceCheck(
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
