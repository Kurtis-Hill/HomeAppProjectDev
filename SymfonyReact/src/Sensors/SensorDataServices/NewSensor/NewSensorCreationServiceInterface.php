<?php

namespace App\Sensors\SensorDataServices\NewSensor;

use App\Sensors\DTO\Internal\Sensor\NewSensorDTO;
use App\Sensors\DTO\Request\AddNewSensorRequestDTO;
use App\Sensors\Entity\Sensor;

interface NewSensorCreationServiceInterface
{
    public function validateNewSensorRequestDTO(AddNewSensorRequestDTO $addNewSensorRequestDTO): array;

    public function validateSensor(Sensor $sensor): array;

    public function createNewSensor(NewSensorDTO $newSensorDTO): Sensor;

    public function saveNewSensor(Sensor $sensor): bool;
}
