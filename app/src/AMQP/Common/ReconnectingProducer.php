<?php

namespace App\AMQP\Common;

use OldSound\RabbitMqBundle\RabbitMq\Producer;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Exception\AMQPIOException;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Wraps a producer used from long-running processes (e.g. the scheduler worker).
 *
 * php-amqplib only sends heartbeats while doing I/O, so a connection that sits idle
 * between publishes gets closed by RabbitMQ ("missed heartbeats from client").
 * To avoid that, this opens the connection when publishing and closes it again straight
 * after, so nothing is left idle. If a publish fails on a dead connection it reconnects
 * and retries once.
 *
 * The connection passed in must be dedicated to this producer, as it is closed after every publish.
 */
readonly class ReconnectingProducer implements ProducerInterface
{
    public function __construct(
        private Producer $producer,
        private AbstractConnection $connection,
        private LoggerInterface $elasticLogger,
    ) {
    }

    public function publish($msgBody, $routingKey = null,$additionalProperties = [], ?array $headers = null): void
    {
        try {
            $this->ensureConnected();

            try {
                $this->producer->publish($msgBody, $routingKey, $additionalProperties, $headers);
            } catch (AMQPRuntimeException|AMQPIOException|AMQPTimeoutException $e) {
                $this->elasticLogger->warning(
                    sprintf('AMQP publish failed, reconnecting and retrying once: %s', $e->getMessage())
                );
                $this->reconnect();
                $this->producer->publish($msgBody, $routingKey, $additionalProperties, $headers);
            }
        } finally {
            $this->closeConnection();
        }
    }

    private function ensureConnected(): void
    {
        if (!$this->connection->isConnected()) {
            $this->reconnect();
        }
    }

    private function reconnect(): void
    {
        // Call the connection directly: the bundle's BaseAmqp::reconnect() is a no-op once the connection is marked closed.
        $this->connection->reconnect();
        $this->producer->setChannel($this->connection->channel());
    }

    private function closeConnection(): void
    {
        try {
            $this->connection->close();
        } catch (Throwable $e) {
            $this->elasticLogger->warning(sprintf('Failed to close AMQP connection after publish: %s', $e->getMessage()));
        }
    }
}
