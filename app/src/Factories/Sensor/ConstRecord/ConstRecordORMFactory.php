<?php

namespace App\Factories\Sensor\ConstRecord;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Exceptions\Sensor\ReadingTypeNotSupportedException;
use App\Repository\Sensor\ConstRecord\ConstantlyRecordRepositoryInterface;
use App\Repository\Sensor\ConstRecord\ORM\ConstantlyRecordAnalogRepository;
use App\Repository\Sensor\ConstRecord\ORM\ConstantlyRecordHumidRepository;
use App\Repository\Sensor\ConstRecord\ORM\ConstantlyRecordLatitudeRepository;
use App\Repository\Sensor\ConstRecord\ORM\ConstantlyRecordTempRepository;

class ConstRecordORMFactory implements ConstRecordFactoryInterface
{
    private ConstantlyRecordAnalogRepository $constAnalogRepository;

    private ConstantlyRecordTempRepository $constTempRepository;

    private ConstantlyRecordHumidRepository $constHumidRepository;

    private ConstantlyRecordLatitudeRepository $constLatitudeRepository;

    public function __construct(
        ConstantlyRecordAnalogRepository $constAnalogRepository,
        ConstantlyRecordTempRepository   $constTempRepository,
        ConstantlyRecordHumidRepository  $constHumidRepository,
        ConstantlyRecordLatitudeRepository $constLatitudeRepository,
    ) {
        $this->constAnalogRepository = $constAnalogRepository;
        $this->constTempRepository = $constTempRepository;
        $this->constHumidRepository = $constHumidRepository;
        $this->constLatitudeRepository = $constLatitudeRepository;
    }

    /**
     * @throws ReadingTypeNotSupportedException
     */
    public function getConstRecordServiceRepository(string $sensorReadingTypeObject): ConstantlyRecordRepositoryInterface
    {
        return match ($sensorReadingTypeObject) {
            Analog::getReadingTypeName() => $this->constAnalogRepository,
            Temperature::getReadingTypeName() => $this->constTempRepository,
            Humidity::getReadingTypeName() => $this->constHumidRepository,
            Latitude::getReadingTypeName() => $this->constLatitudeRepository,
            default => throw new ReadingTypeNotSupportedException(
                ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_UPDATE_APP_MESSAGE
            )
        };
    }
}
