<?php


namespace App\AMQP;


use App\DTOs\SensorDTOs\UpdateSensorReadingDTO;
use App\Entity\Devices\Devices;
use App\ErrorLogs;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\CurrentReading\UpdateCurrentSensorReadingInterface;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\CurrentReading\UpdateCurrentSensorReadingsService;
use App\Repository\Core\DevicesRepository;
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
     * @var DevicesRepository
     */
    private DevicesRepository $deviceRepository;

    /**
     * @param UpdateCurrentSensorReadingInterface $sensorDeviceDataQueueConsumerService
     * @param DevicesRepository $deviceRepository
     */
    public function __construct(
        UpdateCurrentSensorReadingInterface $sensorDeviceDataQueueConsumerService,
        DevicesRepository $deviceRepository
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

        $device = $this->deviceRepository->findOneBy(['deviceNameID' => $sensorData->getDeviceId()]);

        if ($device instanceof Devices) {
            return $this->sensorCurrentReadingUpdateService->handleUpdateCurrentReadingSensorData($sensorData, $device);
        }

        return false;
    }

}
