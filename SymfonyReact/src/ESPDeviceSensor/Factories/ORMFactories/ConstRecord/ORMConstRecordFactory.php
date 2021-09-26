<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\ConstRecord;

use App\Entity\Sensors\ConstantRecording\ConstAnalog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryAnalogRepositroy;
use App\ESPDeviceSensor\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryHumidRepository;
use App\ESPDeviceSensor\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryInterface;
use App\ESPDeviceSensor\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryTempRepository;

class ORMConstRecordFactory implements ORMConstRecordFactoryInterface
{
    private ConstantlyRecordRepositoryAnalogRepositroy $constAnalog;

    private ConstantlyRecordRepositoryTempRepository $constTemp;

    private ConstantlyRecordRepositoryHumidRepository $constHumid;

    public function __construct(
        ConstantlyRecordRepositoryAnalogRepositroy $constAnalog,
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
            ConstAnalog::class => $this->constAnalog,
            Temperature::class => $this->constTemp,
            Humidity::class => $this->constHumid,
        };
    }
}
