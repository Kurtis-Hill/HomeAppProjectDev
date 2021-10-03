<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\ConstRecord;

use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
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
            Analog::class => $this->constAnalog,
            Temperature::class => $this->constTemp,
            Humidity::class => $this->constHumid,
        };
    }
}
