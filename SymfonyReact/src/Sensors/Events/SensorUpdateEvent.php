<?php

namespace App\Sensors\Events;

use App\Sensors\DTO\Internal\Event\SensorUpdateEventDTO;
use Symfony\Contracts\EventDispatcher\Event;

class SensorUpdateEvent extends Event
{
    public const NAME = 'sensor.update';

    public function __construct(
        protected SensorUpdateEventDTO $sensorUpdateEventDTO
    ) {}

    public function getSensorUpdateEventDTO(): SensorUpdateEventDTO
    {
        return $this->sensorUpdateEventDTO;
    }
}
