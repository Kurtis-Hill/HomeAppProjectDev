<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

interface SensorReadingTypesValidatorServiceInterface
{
    public function validateSensorTypeObject(SensorTypeInterface $sensorTypeObject): array;

    #[ArrayShape(['string'])]
    public function validateSensorReadingTypeObject(AllSensorReadingTypeInterface $sensorReadingTypeObject, string $sensorType): array;

    public function validate($object, ExecutionContextInterface $context, $payload);
}
