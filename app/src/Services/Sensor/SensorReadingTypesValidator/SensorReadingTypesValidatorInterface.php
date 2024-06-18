<?php

namespace App\Services\Sensor\SensorReadingTypesValidator;

use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use JetBrains\PhpStorm\ArrayShape;

interface SensorReadingTypesValidatorInterface
{
    /**
     * @throws SensorReadingTypeRepositoryFactoryException
     */
    #[ArrayShape(["errors"])]
    public function validateSensorReadingTypeObjectsBySensorTypeObject(SensorTypeInterface $sensorTypeObject): array;

    #[ArrayShape(["errors"])]
    public function validateSensorReadingTypeObject(AllSensorReadingTypeInterface $sensorReadingTypeObject, string $sensorType): array;
}
