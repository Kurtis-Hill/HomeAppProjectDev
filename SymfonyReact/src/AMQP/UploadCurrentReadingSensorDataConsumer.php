<?php


namespace App\AMQP;


use App\Entity\Devices\Devices;
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
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

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
        $this->em = $entityManager;

        $this->sensorRepository = $entityManager->getRepository(SensorsRepository::class);
        $this->deviceRepository = $entityManager->getRepository(DevicesRepository::class);
    }

    public function execute(AMQPMessage $msg): bool
    {
        $sensorData = unserialize($msg->getBody(), ['allowed_classes' => false]);

        $device = $this->findSensorBelongsToDevice((int)$sensorData['deviceId']);

        if ($device instanceof Devices) {
            $sensorDataHandled = $this->sensorDeviceDataQueueConsumerService->handleUpdateCurrentReadingSensorData($sensorData, $device);

            return $sensorDataHandled;
        }


        return false;
    }

    /**
     * @param int $deviceID
     * @return bool
     */
    private function findSensorBelongsToDevice(int $deviceID): ?Devices
    {
        $device = $this->deviceRepository->findOneBy(['deviceNameID' => $deviceID]);

        if ($device instanceof Devices) {
            return $device;
        }

        return null;
    }

    private function handleSensorDataRequest(array $sensorData): bool
    {

    }

}
