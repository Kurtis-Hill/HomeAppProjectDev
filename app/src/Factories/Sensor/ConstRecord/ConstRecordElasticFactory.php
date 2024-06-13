<?php

namespace App\Factories\Sensor\ConstRecord;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Exceptions\Sensor\ReadingTypeNotSupportedException;
use App\Repository\Sensor\ConstRecord\ConstantlyRecordRepositoryInterface;
use App\Repository\Sensor\ConstRecord\Elastic\ConstRecordAnalogRepository;
use App\Repository\Sensor\ConstRecord\Elastic\ConstRecordHumidityRepository;
use App\Repository\Sensor\ConstRecord\Elastic\ConstRecordLatitudeRepository;
use App\Repository\Sensor\ConstRecord\Elastic\ConstRecordTemperatureRepository;

class ConstRecordElasticFactory implements ConstRecordFactoryInterface
{
    private ConstRecordAnalogRepository $constAnalogRepository;

    private ConstRecordTemperatureRepository $constTempRepository;

    private ConstRecordHumidityRepository $constHumidRepository;

    private ConstRecordLatitudeRepository $constLatitudeRepository;

    public function __construct(
        ConstRecordAnalogRepository $constAnalogRepository,
        ConstRecordTemperatureRepository $constTempRepository,
        ConstRecordHumidityRepository $constHumidRepository,
        ConstRecordLatitudeRepository $constLatitudeRepository,
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
