<?php
declare(strict_types=1);

namespace App\AMQP\Device\Consumers;

use App\DTOs\Device\Internal\DeviceSettingsUpdateDTO;
use App\Exceptions\Sensor\DeviceNotFoundException;
use App\Services\Device\Request\DeviceSettingsUpdateRequestHandler;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

readonly class DeviceSettingsUpdateConsumer implements ConsumerInterface
{
    public function __construct(
        private DeviceSettingsUpdateRequestHandler $deviceSettingsUpdateRequestHandler,
        private LoggerInterface $elasticLogger,
    ) {}

    public function execute(AMQPMessage $msg): int
    {
        try {
            /** @var DeviceSettingsUpdateDTO $deviceUpdateRequestDTO */
            $deviceUpdateRequestDTO = unserialize(
                $msg->getBody(),
                [
                    'allowed_classes' => [
                        DeviceSettingsUpdateDTO::class,
                    ]
                ]
            );

            if (!$deviceUpdateRequestDTO instanceof DeviceSettingsUpdateDTO) {
                $this->elasticLogger->error('Deserialization returned unexpected type, message body may be malformed or sent to wrong queue');

                return self::MSG_REJECT;
            }
        } catch (\Throwable $exception) {
            $this->elasticLogger->error('Deserialization of message failure, check the message has been sent to the correct queue, exception message: ' . $exception->getMessage());

            return self::MSG_ACK;
        }
        try {
            $result = $this->deviceSettingsUpdateRequestHandler->handleDeviceSettingsUpdateRequest($deviceUpdateRequestDTO);
            if ($result === true) {
                $this->elasticLogger->info(sprintf('Device settings update request successful for device %s', $deviceUpdateRequestDTO->getDeviceId()));
            } else {
                $this->elasticLogger->error(sprintf('Device settings update request failed for device %s', $deviceUpdateRequestDTO->getDeviceId()));
            }

            return $result === false ? self::MSG_REJECT : self::MSG_ACK;
        } catch (DeviceNotFoundException) {
            $this->elasticLogger->error('Device settings update request failed, device not found');

            return self::MSG_REJECT;
        } catch (\Throwable $exception) {
            $this->elasticLogger->error('Device settings update request failed with unexpected error, exception message: ' . $exception->getMessage());

            return self::MSG_REJECT;
        }
    }
}
