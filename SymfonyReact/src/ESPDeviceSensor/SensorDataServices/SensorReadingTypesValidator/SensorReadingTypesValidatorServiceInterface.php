<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\SensorReadingTypeRepositoryFactoryException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

interface SensorReadingTypesValidatorServiceInterface
{
    /**
     * @throws SensorReadingTypeRepositoryFactoryException
     */
    #[ArrayShape(["errors"])]
    public function validateSensorReadingTypeObjectsBySensorTypeObject(SensorTypeInterface $sensorTypeObject): array;

    #[ArrayShape(["errors"])]
    public function validateSensorReadingTypeObject(AllSensorReadingTypeInterface $sensorReadingTypeObject, string $sensorType): array;
}
