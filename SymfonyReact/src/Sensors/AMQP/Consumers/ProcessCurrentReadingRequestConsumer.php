<?php
declare(strict_types=1);

namespace App\Sensors\AMQP\Consumers;

use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading\UpdateCurrentSensorReadingInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

readonly class ProcessCurrentReadingRequestConsumer implements ConsumerInterface
{
    public function __construct(
        private UpdateCurrentSensorReadingInterface $sensorDeviceDataQueueConsumerService,
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $elasticLogger,
    ) {}

    // @ADD new current reading type dtos to allowed_classes array
    public function execute(AMQPMessage $msg): bool
    {
        try {
            /** @var UpdateSensorCurrentReadingMessageDTO $sensorData */
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
            $this->elasticLogger->error('Deserialization of message failure, check the message has been sent to the correct queue, exception message: ' . $exception->getMessage());

            return true;
        }
        try {
            $device = $this->deviceRepository->find($sensorData->getDeviceID());
        } catch (ORMException $exception) {
            $this->elasticLogger->error('expection message: ' . $exception->getMessage());

            return false;
        } catch (Exception $e) {
            $this->elasticLogger->error('expection message: ' . $e->getMessage());

            return true;
        }

        if ($device instanceof Devices) {
            try {
                $validationErrors = $this->sensorDeviceDataQueueConsumerService->handleUpdateSensorCurrentReading(
                    $sensorData,
                    $device
                );
                if ($validationErrors) {
                    $this->elasticLogger->error('Validation errors', ['errors' => $validationErrors]);
                }
            } catch (ORMException|OptimisticLockException $e) {
                $this->elasticLogger->error($e->getMessage(), ['device' => $device->getUserIdentifier()]);

                return false;
            } catch (Exception $e) {
                $this->elasticLogger->error($e->getMessage(), ['device' => $device->getUserIdentifier()]);

                return true;
            }
        }

        return true;
    }
}
