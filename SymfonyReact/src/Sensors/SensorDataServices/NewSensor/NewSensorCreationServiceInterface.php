<?php

namespace App\Sensors\SensorDataServices\NewSensor;

use App\Devices\Entity\Devices;
use App\Sensors\DTO\Request\AddNewSensorRequestDTO;
use App\Sensors\DTO\Sensor\NewSensorDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\DuplicateSensorException;
use App\Sensors\Exceptions\SensorTypeException;
use Doctrine\ORM\ORMException;

interface NewSensorCreationServiceInterface
{
    public function validateNewSensorRequestDTO(AddNewSensorRequestDTO $addNewSensorRequestDTO): array;

    public function validateSensor(Sensor $sensor): array;

    public function createNewSensor(NewSensorDTO $newSensorDTO): Sensor;

    public function saveNewSensor(Sensor $sensor): bool;
}
