<?php

namespace App\Sensors\Factories\ConstRecord;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Repository\ConstRecord\ConstantlyRecordRepositoryInterface;
use App\Sensors\Repository\ConstRecord\ORM\ConstantlyRecordAnalogRepository;
use App\Sensors\Repository\ConstRecord\ORM\ConstantlyRecordHumidRepository;
use App\Sensors\Repository\ConstRecord\ORM\ConstantlyRecordLatitudeRepository;
use App\Sensors\Repository\ConstRecord\ORM\ConstantlyRecordTempRepository;

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
