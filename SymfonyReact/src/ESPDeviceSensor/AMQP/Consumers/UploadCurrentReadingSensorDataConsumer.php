<?php


namespace App\ESPDeviceSensor\AMQP\Consumers;

use App\Devices\Entity\Devices;
use App\ErrorLogs;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ESPDeviceSensor\DTO\Sensor\CurrentReadingDTO\UpdateSensorCurrentReadingConsumerMessageDTO;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\CurrentReading\UpdateCurrentSensorReadingInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class UploadCurrentReadingSensorDataConsumer implements ConsumerInterface
{
    private UpdateCurrentSensorReadingInterface $sensorCurrentReadingUpdateService;

    private DeviceRepositoryInterface $deviceRepository;

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
            $sensorData = unserialize(
                $msg->getBody(),
                ['allowed_classes' => [UpdateSensorCurrentReadingConsumerMessageDTO::class]]
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
        } catch (NonUniqueResultException | ORMException $e) {
            return true;
        }

        if ($device instanceof Devices) {
            return $this->sensorCurrentReadingUpdateService->handleUpdateSensorCurrentReading(
                $sensorData,
                $device
            );
        }

        return false;
    }
}