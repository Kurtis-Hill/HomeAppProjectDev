<?php
declare(strict_types=1);

namespace App\Services\Device\DeleteDevice;

use App\Entity\Device\Devices;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Services\API\APIErrorMessages;
use Doctrine\ORM\Exception\ORMException;
use Psr\Log\LoggerInterface;

readonly class DeleteDeviceService implements DeleteDeviceServiceInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger
    ) {
    }

    public function deleteDevice(Devices $devices): bool
    {
        try {
            $deviceID = $devices->getDeviceID();
            $this->deviceRepository->remove($devices);
            $this->deviceRepository->flush();

            $this->logger->info(sprintf('Device with id %d deleted', $deviceID));
        } catch (ORMException) {
            $this->logger->error(sprintf(APIErrorMessages::QUERY_FAILURE, 'Delete device'));

            return false;
        }

        return true;
    }
}
