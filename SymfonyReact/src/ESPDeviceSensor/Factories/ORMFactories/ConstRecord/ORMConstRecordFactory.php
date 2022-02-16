<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\ConstRecord;

use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotSupportedException;
use App\ESPDeviceSensor\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryAnalogRepository;
use App\ESPDeviceSensor\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryHumidRepository;
use App\ESPDeviceSensor\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryInterface;
use App\ESPDeviceSensor\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryTempRepository;

class ORMConstRecordFactory implements ORMConstRecordFactoryInterface
{
    private ConstantlyRecordRepositoryAnalogRepository $constAnalog;

    private ConstantlyRecordRepositoryTempRepository $constTemp;

    private ConstantlyRecordRepositoryHumidRepository $constHumid;

    public function __construct(
        ConstantlyRecordRepositoryAnalogRepository $constAnalog,
        ConstantlyRecordRepositoryTempRepository   $constTemp,
        ConstantlyRecordRepositoryHumidRepository  $constHumid,
    )
    {
        $this->constAnalog = $constAnalog;
        $this->constTemp = $constTemp;
        $this->constHumid = $constHumid;
    }

    public function getConstRecordServiceRepository(string $sensorReadingTypeObject): ConstantlyRecordRepositoryInterface
    {
        return match ($sensorReadingTypeObject) {
            Analog::READING_TYPE => $this->constAnalog,
            Temperature::READING_TYPE => $this->constTemp,
            Humidity::READING_TYPE => $this->constHumid,
            default => throw new ReadingTypeNotSupportedException(
                ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_UPDATE_APP_MESSAGE
            )
        };
    }
}
