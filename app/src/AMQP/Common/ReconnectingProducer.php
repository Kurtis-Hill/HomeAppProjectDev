<?php

namespace App\AMQP\Common;

use OldSound\RabbitMqBundle\RabbitMq\Producer;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Exception\AMQPIOException;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use Psr\Log\LoggerInterface;

/**
 * Wraps a producer used from long-running processes (e.g. the scheduler worker).
 *
 * php-amqplib only sends heartbeats while doing I/O, so a producer that sits idle
 * between publishes has its connection closed by RabbitMQ ("missed heartbeats from client").
 * This reconnects before publishing when the connection has been idle too long, and
 * reconnects + retries once if the publish still fails on a dead connection.
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
        $this->ensureConnectionIsAlive();

        try {
            $this->producer->publish($msgBody, $routingKey, $additionalProperties, $headers);
        } catch (AMQPRuntimeException|AMQPIOException|AMQPTimeoutException $e) {
            $this->elasticLogger->warning(
                sprintf('AMQP publish failed, reconnecting and retrying once: %s', $e->getMessage())
            );
            $this->reconnect();
            $this->producer->publish($msgBody, $routingKey, $additionalProperties, $headers);
        }
    }

    private function ensureConnectionIsAlive(): void
    {
        if (!$this->connection->isConnected()) {
            $this->reconnect();

            return;
        }

        $heartbeat = $this->connection->getHeartbeat();
        $lastActivity = $this->connection->getLastActivity();
        // Past half the heartbeat interval RabbitMQ may already have dropped us, so start fresh.
        if ($heartbeat > 0 && $lastActivity > 0 && (microtime(true) - $lastActivity) > ($heartbeat / 2)) {
            $this->reconnect();
        }
    }

    private function reconnect(): void
    {
        // Call the connection directly: the bundle's BaseAmqp::reconnect() is a no-op once the connection is marked closed.
        $this->connection->reconnect();
        $this->producer->setChannel($this->connection->channel());
    }
}
