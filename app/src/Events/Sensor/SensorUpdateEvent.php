<?php

namespace App\Events\Sensor;

use App\DTOs\Sensor\Internal\Event\SensorUpdateEventDTO;
use Symfony\Contracts\EventDispatcher\Event;

class SensorUpdateEvent extends Event
{
    public const NAME = 'sensor.update';

    public function __construct(
        private readonly SensorUpdateEventDTO $sensorUpdateEventDTO
    ) {
    }

    public function getSensorUpdateEventDTO(): SensorUpdateEventDTO
    {
        return $this->sensorUpdateEventDTO;
    }
}
