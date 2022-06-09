<?php

namespace App\Sensors\Factories\ORMFactories\ConstRecord;

use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryAnalogRepository;
use App\Sensors\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryHumidRepository;
use App\Sensors\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryInterface;
use App\Sensors\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryLatitudeRepository;
use App\Sensors\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryTempRepository;

class ORMConstRecordFactory
{
    private ConstantlyRecordRepositoryAnalogRepository $constAnalog;

    private ConstantlyRecordRepositoryTempRepository $constTemp;

    private ConstantlyRecordRepositoryHumidRepository $constHumid;

    private ConstantlyRecordRepositoryLatitudeRepository $constLatitude;

    public function __construct(
        ConstantlyRecordRepositoryAnalogRepository $constAnalog,
        ConstantlyRecordRepositoryTempRepository   $constTemp,
        ConstantlyRecordRepositoryHumidRepository  $constHumid,
        ConstantlyRecordRepositoryLatitudeRepository $constLatitude,
    ) {
        $this->constAnalog = $constAnalog;
        $this->constTemp = $constTemp;
        $this->constHumid = $constHumid;
        $this->constLatitude = $constLatitude;
    }

    public function getConstRecordServiceRepository(string $sensorReadingTypeObject): ConstantlyRecordRepositoryInterface
    {
        return match ($sensorReadingTypeObject) {
            Analog::getReadingTypeName() => $this->constAnalog,
            Temperature::getReadingTypeName() => $this->constTemp,
            Humidity::getReadingTypeName() => $this->constHumid,
            Latitude::getReadingTypeName() => $this->constLatitude,
            default => throw new ReadingTypeNotSupportedException(
                ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_UPDATE_APP_MESSAGE
            )
        };
    }
}
