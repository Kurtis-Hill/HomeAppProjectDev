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
    private ConstantlyRecordRepositoryAnalogRepository $constAnalogRepository;

    private ConstantlyRecordRepositoryTempRepository $constTempRepository;

    private ConstantlyRecordRepositoryHumidRepository $constHumidRepository;

    private ConstantlyRecordRepositoryLatitudeRepository $constLatitudeRepository;

    public function __construct(
        ConstantlyRecordRepositoryAnalogRepository $constAnalogRepository,
        ConstantlyRecordRepositoryTempRepository   $constTempRepository,
        ConstantlyRecordRepositoryHumidRepository  $constHumidRepository,
        ConstantlyRecordRepositoryLatitudeRepository $constLatitudeRepository,
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
