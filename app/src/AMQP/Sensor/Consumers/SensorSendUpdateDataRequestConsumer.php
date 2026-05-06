<?php
declare(strict_types=1);

namespace App\AMQP\Sensor\Consumers;

use App\Builders\Sensor\Internal\SensorUpdateRequestDTOBuilder\SingleSensorUpdateRequestDTOBuilder;
use App\DTOs\Sensor\Internal\Event\SensorUpdateEventDTO;
use App\Exceptions\Sensor\SensorNotFoundException;
use App\Exceptions\Sensor\SensorRequestException;
use App\Repository\Sensor\Sensors\ORM\SensorRepository;
use App\Services\Sensor\UpdateDeviceSensorData\UpdateDeviceSensorDataHandler;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

readonly class SensorSendUpdateDataRequestConsumer implements ConsumerInterface
{
    public function __construct(
        private UpdateDeviceSensorDataHandler $updateDeviceSensorDataHandler,
        private SensorRepository $sensorRepository,
        private SingleSensorUpdateRequestDTOBuilder $singleSensorUpdateRequestDTOBuilder,
        private LoggerInterface $logger,
    ) {}

    public function execute(AMQPMessage $msg): int
    {
        try {
            /** @var SensorUpdateEventDTO $sensorUpdateEventDTO */
            $sensorUpdateEventDTO = unserialize(
                $msg->getBody(),
                [
                    'allowed_classes' => [
                        SensorUpdateEventDTO::class]
                ]
            );
        } catch (Exception $exception) {
            $this->logger->error('Deserialization of message failure, check the message has been sent to the correct queue, exception message: ' . $exception->getMessage());

            return self::MSG_ACK;
        }

        $sensor = $this->sensorRepository->find($sensorUpdateEventDTO->getSensorID());
        $sensorsToUpdate = $this->sensorRepository->findSameSensorTypesOnSameDevice(
            $sensor->getDevice()->getDeviceID(),
            $sensor->getSensorTypeObject()->getSensorTypeID(),
        );

        $sensorUpdateRequestDTOsByDeviceID = [];
        foreach ($sensorsToUpdate as $sensorToUpdate) {
            $sensorUpdateRequestDTOsByDeviceID[] = $this->singleSensorUpdateRequestDTOBuilder->buildSensorUpdateRequestDTO($sensorToUpdate);
        }

        try {
            $updateRequestResult = $this->updateDeviceSensorDataHandler->handleSensorsUpdateRequest($sensorUpdateRequestDTOsByDeviceID);
            if ($updateRequestResult === false) {
                $this->logger->error('Error processing sensor data to upload sensor not found, sensor ID: ' . $sensorUpdateEventDTO->getSensorID());
            } else {
                $this->logger->info('Sensor data update handled successfully, sensor ID: ' . $sensorUpdateEventDTO->getSensorID());
            }

            return $updateRequestResult === true
                ? self::MSG_ACK
                : self::MSG_REJECT;
        } catch (SensorRequestException $e) {
            $this->logger->error('Sensor request exception, exception message: ' . $e->getMessage());

            return self::MSG_REJECT;
        } catch (SensorNotFoundException $e) {
            $this->logger->error($e->getMessage());
        }

        return self::MSG_ACK;
    }
}
