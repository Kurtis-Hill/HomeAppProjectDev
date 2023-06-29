<?php

namespace App\Sensors\AMQP\Consumers;

use App\Common\API\Traits\HomeAppAPITrait;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading\UpdateCurrentSensorReadingInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class UploadCurrentReadingSensorDataConsumer implements ConsumerInterface
{
    use HomeAppAPITrait;

    private UpdateCurrentSensorReadingInterface $sensorCurrentReadingUpdateService;

    private DeviceRepositoryInterface $deviceRepository;

    private LoggerInterface $logger;

    public function __construct(
        UpdateCurrentSensorReadingInterface $sensorDeviceDataQueueConsumerService,
        DeviceRepositoryInterface $deviceRepository,
        LoggerInterface $elasticLogger,
    ) {
        $this->sensorCurrentReadingUpdateService = $sensorDeviceDataQueueConsumerService;
        $this->deviceRepository = $deviceRepository;
        $this->logger = $elasticLogger;
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
                        BoolCurrentReadingUpdateRequestDTO::class,
                    ]
                ]
            );
        } catch (Exception $exception) {
            $this->logger->error('Deserialization of message failure, check the message has been sent to the correct queue, exception message: ' . $exception->getMessage());

            return true;
        }
        try {
            $device = $this->deviceRepository->find($sensorData->getDeviceId());
        } catch (NonUniqueResultException | ORMException $exception) {
            $this->logger->error('expection message: ' . $exception->getMessage());

            return true;
        }

        if ($device instanceof Devices) {
            try {
                return $this->sensorCurrentReadingUpdateService->handleUpdateSensorCurrentReading(
                    $sensorData,
                    $device
                );
            } catch (ORMException | OptimisticLockException $e) {
                $this->logger->error($e->getMessage(), ['device' => $device->getUserIdentifier()]);

                return false;
            } catch (Exception $e) {
                $this->logger->error($e->getMessage(), ['device' => $device->getUserIdentifier()]);

                return true;
            }
        }

        return true;
    }
}
