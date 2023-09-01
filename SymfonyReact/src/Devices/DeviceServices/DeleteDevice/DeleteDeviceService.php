<?php

namespace App\Devices\DeviceServices\DeleteDevice;

use App\Common\API\APIErrorMessages;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Psr\Log\LoggerInterface;

class DeleteDeviceService implements DeleteDeviceServiceInterface
{
    private DeviceRepositoryInterface $deviceRepository;

    private LoggerInterface $logger;

    public function __construct(DeviceRepositoryInterface $deviceRepository, LoggerInterface $elasticLogger)
    {
        $this->deviceRepository = $deviceRepository;
    }

    public function deleteDevice(Devices $devices): bool
    {
        try {
            $this->deviceRepository->remove($devices);
            $this->deviceRepository->flush();
        } catch (ORMException) {
            $this->logger->error(sprintf(APIErrorMessages::QUERY_FAILURE, 'Delete device'));

            return false;
        }

        return true;
    }
}
