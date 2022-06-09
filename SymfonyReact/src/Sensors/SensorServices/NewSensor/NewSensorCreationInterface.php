<?php

namespace App\Sensors\SensorServices\NewSensor;

use App\Sensors\DTO\Internal\Sensor\NewSensorDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\UserNotAllowedException;
use JetBrains\PhpStorm\ArrayShape;

interface NewSensorCreationInterface
{
    /**
     * @throws UserNotAllowedException
     */
    #[ArrayShape(['validationErrors'])]
    public function processNewSensor(NewSensorDTO $newSensorDTO): array;

    public function saveSensor(Sensor $sensor): bool;
}
