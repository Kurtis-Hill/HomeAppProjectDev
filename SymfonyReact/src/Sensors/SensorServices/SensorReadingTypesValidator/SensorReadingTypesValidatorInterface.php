<?php

namespace App\Sensors\SensorServices\SensorReadingTypesValidator;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
