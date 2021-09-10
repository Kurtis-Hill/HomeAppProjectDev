<?php


namespace App\AMQP;


use App\Entity\Devices\Devices;
use App\Entity\Sensors\Sensors;
use App\Repository\Core\DevicesRepository;
use App\Repository\Core\SensorsRepository;
use App\Services\ESPDeviceSensor\SensorData\SensorDeviceDataQueueConsumerService;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class UploadCurrentReadingSensorDataConsumer implements ConsumerInterface
{
    /**
     * @var SensorDeviceDataQueueConsumerService
     */
    private SensorDeviceDataQueueConsumerService $sensorDeviceDataQueueConsumerService;

    /**
     * @var SensorsRepository
     */
    private SensorsRepository $sensorRepository;

    /**
     * @var DevicesRepository
     */
    private DevicesRepository $deviceRepository;

    /**
     * @param SensorDeviceDataQueueConsumerService $sensorDeviceDataQueueConsumerService
     */
    public function __construct(
        SensorDeviceDataQueueConsumerService $sensorDeviceDataQueueConsumerService,
        EntityManagerInterface $entityManager
    ) {
        $this->sensorDeviceDataQueueConsumerService = $sensorDeviceDataQueueConsumerService;

        $this->sensorRepository = $entityManager->getRepository(Sensors::class);
        $this->deviceRepository = $entityManager->getRepository(Devices::class);
    }

    public function execute(AMQPMessage $msg): bool
    {
        $sensorData = unserialize($msg->getBody(), ['allowed_classes' => false]);
//dd($sensorData);
        $device = $this->deviceRepository->findOneBy(['deviceNameID' => (int)$sensorData['deviceId']]);
//dd($device);
        if ($device instanceof Devices) {
            return $this->sensorDeviceDataQueueConsumerService->handleUpdateCurrentReadingSensorData($sensorData, $device);
        }

        return false;
    }

}
