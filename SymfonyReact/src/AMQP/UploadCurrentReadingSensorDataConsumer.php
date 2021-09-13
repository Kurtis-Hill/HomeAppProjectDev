<?php


namespace App\AMQP;


use App\DTOs\SensorDTOs\UpdateSensorReadingDTO;
use App\Entity\Devices\Devices;
use App\ErrorLogs;
use App\Repository\Core\DevicesRepository;
use App\Services\ESPDeviceSensor\SensorData\UpdateCurrentSensorReadingsService;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class UploadCurrentReadingSensorDataConsumer implements ConsumerInterface
{
    /**
     * @var UpdateCurrentSensorReadingsService
     */
    private UpdateCurrentSensorReadingsService $sensorDeviceDataQueueConsumerService;

    /**
     * @var DevicesRepository
     */
    private DevicesRepository $deviceRepository;

    /**
     * @param UpdateCurrentSensorReadingsService $sensorDeviceDataQueueConsumerService
     * @param DevicesRepository $deviceRepository
     */
    public function __construct(
        UpdateCurrentSensorReadingsService $sensorDeviceDataQueueConsumerService,
        DevicesRepository $deviceRepository
    ) {
        $this->sensorDeviceDataQueueConsumerService = $sensorDeviceDataQueueConsumerService;
        $this->deviceRepository = $deviceRepository;
    }

    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    public function execute(AMQPMessage $msg): bool
    {
        try {
            $sensorData = unserialize($msg->getBody(), ['allowed_classes' => UpdateSensorReadingDTO::class]);
        } catch (Exception $exception) {
            error_log(
                'Deserialization of message failure, check the message has been sent to the correct queue, exception message: ' . $exception->getMessage(),
                ErrorLogs::SERVER_ERROR_LOG_LOCATION
            );
            return true;
        }

        $device = $this->deviceRepository->findOneBy(['deviceNameID' => $sensorData->getDeviceId()]);

        if ($device instanceof Devices) {
            return $this->sensorDeviceDataQueueConsumerService->handleUpdateCurrentReadingSensorData($sensorData, $device);
        }

        return false;
    }

}
