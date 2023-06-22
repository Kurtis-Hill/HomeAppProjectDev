<?php

namespace App\Sensors\Factories\ConstRecord;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Repository\ConstRecord\ConstantlyRecordRepositoryInterface;
use App\Sensors\Repository\ConstRecord\Elastic\ConstRecordAnalogRepository;
use App\Sensors\Repository\ConstRecord\Elastic\ConstRecordHumidityRepository;
use App\Sensors\Repository\ConstRecord\Elastic\ConstRecordLatitudeRepository;
use App\Sensors\Repository\ConstRecord\Elastic\ConstRecordTemperatureRepository;

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
