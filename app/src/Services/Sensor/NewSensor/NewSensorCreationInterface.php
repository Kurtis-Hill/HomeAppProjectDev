<?php

namespace App\Services\Sensor\NewSensor;

use App\DTOs\Sensor\Internal\Sensor\NewSensorDTO;
use App\Entity\Sensor\Sensor;
use App\Entity\User\User;
use App\Exceptions\Sensor\UserNotAllowedException;
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
