<?php

declare(strict_types=1);

namespace App\Events\Sensor;

use App\DTOs\Sensor\Internal\Event\SensorDeletionEventDTO;
use Symfony\Contracts\EventDispatcher\Event;

class SensorDeleteEvent extends Event
{
    public const NAME = 'sensor.delete';

    public function __construct(private SensorDeletionEventDTO $sensorDeletionEventDTO)
    {
    }

    public function getSensorDeletionEventDTO(): SensorDeletionEventDTO
    {
        return $this->sensorDeletionEventDTO;
    }
}
