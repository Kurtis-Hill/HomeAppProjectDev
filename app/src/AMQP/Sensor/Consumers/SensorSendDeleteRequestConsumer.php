<?php

declare(strict_types=1);

namespace App\AMQP\Sensor\Consumers;

use App\Builders\Device\Request\DeviceRequestEncapsulationBuilder;
use App\Builders\Sensor\Request\SensorUpdateBuilders\SensorDeletionDTOBuilder;
use App\DTOs\Sensor\Internal\Event\SensorDeletionEventDTO;
use App\DTOs\Sensor\Internal\Event\SensorUpdateEventDTO;
use App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate\SingleSensorUpdateRequestDTO;
use App\Repository\Device\ORM\DeviceRepository;
use App\Services\Device\Request\DeviceRequestHandlerInterface;
use App\Traits\ValidatorProcessorTrait;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SensorSendDeleteRequestConsumer implements ConsumerInterface
{
    use ValidatorProcessorTrait;

    private const URI = '/delete-sensor';

    public function __construct(
        private DeviceRepository $deviceRepository,
        private SensorDeletionDTOBuilder $deletionDTOBuilder,
        private DeviceRequestHandlerInterface $deviceRequestHandler,
        private ValidatorInterface $validator,
        private LoggerInterface $logger,
    ) {
    }

    public function execute(AMQPMessage $msg): int
    {
        try {
            /** @var SensorDeletionEventDTO $sensorUpdateEventDTO */
            $sensorUpdateEventDTO = unserialize(
                $msg->getBody(),
                [
                    'allowed_classes' => [
                        SensorDeletionEventDTO::class,
                    ]
                ]
            );

            $sensorDeleteRequestDTO = $this->deletionDTOBuilder->buildSensorDeletionDTO($sensorUpdateEventDTO->getSensorType());

            $errors = $this->validator->validate($sensorDeleteRequestDTO);
            if ($this->checkIfErrorsArePresent($errors)) {
                $this->logger->error('Validation of message failure, check the message has been sent to the correct queue' . implode(', ', $this->getValidationErrorAsArray($errors)));

                return self::MSG_ACK;
            }

            $device = $this->deviceRepository->find($sensorUpdateEventDTO->getDeviceID());
            $deviceDeletionEncapsulationDTO = DeviceRequestEncapsulationBuilder::buildDeviceRequestEncapsulation(
                $device,
                $sensorDeleteRequestDTO,
                self::URI,
            );
        } catch (Exception $exception) {
            $this->logger->error('Deserialization of message failure, check the message has been sent to the correct queue, exception message: ' . $exception->getMessage());

            return self::MSG_ACK;
        }

        try {
            $deviceResponse = $this->deviceRequestHandler->handleDeviceRequest(
                $deviceDeletionEncapsulationDTO,
                [],
            );
            if ($deviceResponse->getStatusCode() !== Response::HTTP_OK) {
                $this->logger->error(sprintf('Error processing sensor delete request'));
                return self::MSG_ACK;
            }
        } catch (Exception $e) {
            $this->logger->error(sprintf('Error processing sensor data to upload exception: %s', $e->getMessage()));
        }

        return self::MSG_ACK;
    }
}
