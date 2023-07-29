<?php

namespace App\Sensors\AMQP\Consumers;

use App\Common\API\Traits\HomeAppAPITrait;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ErrorLogs;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\Sensors\SensorDataServices\SensorReadingUpdate\CurrentReading\UpdateCurrentSensorReadingInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class UploadCurrentReadingSensorDataConsumer implements ConsumerInterface
{
    use HomeAppAPITrait;

    private UpdateCurrentSensorReadingInterface $sensorCurrentReadingUpdateService;

    private DeviceRepositoryInterface $deviceRepository;

    public function __construct(
        UpdateCurrentSensorReadingInterface $sensorDeviceDataQueueConsumerService,
        DeviceRepositoryInterface $deviceRepository
    ) {
        $this->sensorCurrentReadingUpdateService = $sensorDeviceDataQueueConsumerService;
        $this->deviceRepository = $deviceRepository;
    }

    // @ADD new current reading type dtos to allowed_classes array
    public function execute(AMQPMessage $msg): bool
    {
        try {
            $sensorData = unserialize(
                $msg->getBody(),
                [
                    'allowed_classes' => [
                        UpdateSensorCurrentReadingMessageDTO::class,
                        AnalogCurrentReadingUpdateRequestDTO::class,
                        HumidityCurrentReadingUpdateRequestDTO::class,
                        LatitudeCurrentReadingUpdateRequestDTO::class,
                        TemperatureCurrentReadingUpdateRequestDTO::class,
                    ]
                ]
            );
        } catch (Exception $exception) {
            error_log(
                'Deserialization of message failure, check the message has been sent to the correct queue, exception message: ' . $exception->getMessage(),
                0,
                ErrorLogs::SERVER_ERROR_LOG_LOCATION
            );
            return true;
        }
        try {
            $device = $this->deviceRepository->findOneById($sensorData->getDeviceId());
        } catch (NonUniqueResultException | ORMException $exception) {
            error_log(
                'expection message: ' . $exception->getMessage(),
                0,
                ErrorLogs::SERVER_ERROR_LOG_LOCATION
            );

            return true;
        }

        if ($device instanceof Devices) {
            try {
                return $this->sensorCurrentReadingUpdateService->handleUpdateSensorCurrentReading(
                    $sensorData,
                    $device
                );
            } catch (ORMException|OptimisticLockException) {
                return false;
            }
        }

        return true;
    }
}
