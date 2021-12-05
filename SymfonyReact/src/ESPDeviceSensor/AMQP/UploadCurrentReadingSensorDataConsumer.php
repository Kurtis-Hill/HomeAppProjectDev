<?php


namespace App\ESPDeviceSensor\AMQP;

use App\Devices\Entity\Devices;
use App\ErrorLogs;
use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorReadingDTO;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\CurrentReading\UpdateCurrentSensorReadingInterface;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\CurrentReading\UpdateCurrentSensorReadingsService;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class UploadCurrentReadingSensorDataConsumer implements ConsumerInterface
{
    /**
     * @var UpdateCurrentSensorReadingsService
     */
    private UpdateCurrentSensorReadingsService $sensorCurrentReadingUpdateService;

    /**
     * @var DeviceRepositoryInterface
     */
    private DeviceRepositoryInterface $deviceRepository;

    /**
     * @param UpdateCurrentSensorReadingInterface $sensorDeviceDataQueueConsumerService
     * @param DeviceRepositoryInterface $deviceRepository
     */
    public function __construct(
        UpdateCurrentSensorReadingInterface $sensorDeviceDataQueueConsumerService,
        DeviceRepositoryInterface $deviceRepository
    ) {
        $this->sensorCurrentReadingUpdateService = $sensorDeviceDataQueueConsumerService;
        $this->deviceRepository = $deviceRepository;
    }

    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    public function execute(AMQPMessage $msg): bool
    {
        try {
            $sensorData = unserialize($msg->getBody(), ['allowed_classes' => [UpdateSensorReadingDTO::class]]);
        } catch (Exception $exception) {
            error_log(
                'Deserialization of message failure, check the message has been sent to the correct queue, exception message: ' . $exception->getMessage(),
                0,
                ErrorLogs::SERVER_ERROR_LOG_LOCATION
            );

            return true;
        }

        $device = $this->deviceRepository->findOneById($sensorData->getDeviceId());

        if ($device instanceof Devices) {
            return $this->sensorCurrentReadingUpdateService->handleUpdateSensorCurrentReading($sensorData, $device);
        }

        return false;
    }
}
