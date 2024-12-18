<?php

namespace App\DTOs\UserInterface\RequestDTO;

use App\Services\CustomValidators\Sensor\SensorRequestValidators\ReadingTypeRequestConstraint;
use App\Services\CustomValidators\Sensor\SensorRequestValidators\SensorTypeRequestConstraint;
use Symfony\Component\Validator\Constraints as Assert;

class CardViewFilterRequestDTO
{
    #[
        Assert\Type(type: ['array', "null"], message: 'sensorTypes must be an {{ type }} you have provided {{ value }}'),
        SensorTypeRequestConstraint
    ]
    private mixed $sensorTypes = [];

    #[
        Assert\Type(type: ['array', "null"], message: 'readingTypes must be an {{ type }} you have provided {{ value }}'),
        ReadingTypeRequestConstraint
    ]
    private mixed $readingTypes = [];

    public function getSensorTypes(): mixed
    {
        return $this->sensorTypes;
    }

    public function setSensorTypes(mixed $sensorTypes): void
    {
        $this->sensorTypes = $sensorTypes;
    }

    public function getReadingTypes(): mixed
    {
        return $this->readingTypes;
    }

    public function setReadingTypes(mixed $readingTypes): void
    {
        $this->readingTypes = $readingTypes;
    }

//    public static function checkIfAllReadingTypesAreSelected(): bool
//    {
//        $missingReadingTypes = array_diff(
//            ReadingTypes::SENSOR_READING_TYPE_DATA,
//            self::readingTypes
//        );
//
//        return empty($missingReadingTypes);
//    }

//    public static function checkIfAllSensorTypesAreSelected(): bool
//    {
//        $missingSensorTypes = array_diff(
//            SensorType::ALL_SENSOR_TYPES,
//            self::sensorTypes
//        );
//
//        return empty($missingSensorTypes);
//    }
}
