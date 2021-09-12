<?php


namespace App\AMQP;


use App\Entity\Devices\Devices;
use App\Repository\Core\DevicesRepository;
use App\Services\ESPDeviceSensor\SensorData\SensorDeviceDataQueueConsumerUpdateService;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class UploadCurrentReadingSensorDataConsumer implements ConsumerInterface
{
    /**
     * @var SensorDeviceDataQueueConsumerUpdateService
     */
    private SensorDeviceDataQueueConsumerUpdateService $sensorDeviceDataQueueConsumerService;

    /**
     * @var DevicesRepository
     */
    private DevicesRepository $deviceRepository;

    /**
     * @param SensorDeviceDataQueueConsumerUpdateService $sensorDeviceDataQueueConsumerService
     * @param DevicesRepository $deviceRepository
     */
    public function __construct(
        SensorDeviceDataQueueConsumerUpdateService $sensorDeviceDataQueueConsumerService,
        DevicesRepository $deviceRepository
    ) {
        $this->sensorDeviceDataQueueConsumerService = $sensorDeviceDataQueueConsumerService;
        $this->deviceRepository = $deviceRepository;
    }

    public function execute(AMQPMessage $msg): bool
    {
        $sensorData = unserialize($msg->getBody(), ['allowed_classes' => false]);
        $device = $this->deviceRepository->findOneBy(['deviceNameID' => (int)$sensorData['deviceId']]);

        if ($device instanceof Devices) {
            return $this->sensorDeviceDataQueueConsumerService->handleUpdateCurrentReadingSensorData($sensorData, $device);
        }

        return false;
    }

}
