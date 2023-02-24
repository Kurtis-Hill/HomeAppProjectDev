<?php

namespace App\Sensors\SensorServices\NewSensor;

use App\Sensors\DTO\Internal\Sensor\NewSensorDTO;
use App\Sensors\DTO\Request\AddNewSensorRequestDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\UserNotAllowedException;
use App\User\Entity\User;
use JetBrains\PhpStorm\ArrayShape;

interface NewSensorCreationInterface
{
    public function buildNewSensorDTO(AddNewSensorRequestDTO $newSensorRequestDTO, User $user): NewSensorDTO;

    /**
     * @throws UserNotAllowedException
     */
    #[ArrayShape(['validationErrors'])]
    public function processNewSensor(NewSensorDTO $newSensorDTO): array;

    public function saveSensor(Sensor $sensor): bool;
}
