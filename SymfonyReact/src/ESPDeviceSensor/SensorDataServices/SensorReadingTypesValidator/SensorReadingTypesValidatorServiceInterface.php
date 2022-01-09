<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

interface SensorReadingTypesValidatorServiceInterface
{
    public function validateSensorTypeObject(SensorTypeInterface $sensorTypeObject): array;


    public function validateSensorReadingTypeObject(AllSensorReadingTypeInterface $sensorReadingTypeObject, string $sensorType): array;

//    #[Assert\Callback]
    public static function validate(
//        $object,
        ExecutionContextInterface $context,
        $payload
    );
}
