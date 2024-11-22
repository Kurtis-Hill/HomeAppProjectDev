<?php
declare(strict_types=1);

namespace App\AMQP\Sensor\Consumers;

use App\DTOs\Sensor\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingTransportMessageDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\Entity\Device\Devices;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Services\Sensor\SensorReadingUpdate\CurrentReading\UpdateCurrentSensorReadingInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
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
            /** @var UpdateSensorCurrentReadingTransportMessageDTO $sensorData */
            $sensorData = unserialize(
                $msg->getBody(),
                [
                    'allowed_classes' => [
                        UpdateSensorCurrentReadingTransportMessageDTO::class,
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
