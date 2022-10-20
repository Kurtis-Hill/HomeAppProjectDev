<?php

namespace App\Sensors\Factories\SensorReadingType;

use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Repository\ReadingType\ORM\AnalogRepository;
use App\Sensors\Repository\ReadingType\ORM\HumidityRepository;
use App\Sensors\Repository\ReadingType\ORM\LatitudeRepository;
use App\Sensors\Repository\ReadingType\ORM\TemperatureRepository;
use App\Sensors\Repository\ReadingType\ReadingTypeRepositoryInterface;

class SensorReadingTypeRepositoryFactory
{
    private TemperatureRepository $temperatureRepository;

    private HumidityRepository $humidityRepository;

    private AnalogRepository $analogRepository;

    private LatitudeRepository $latitudeRepository;

    public function __construct(
        TemperatureRepository $temperatureRepository,
        HumidityRepository $humidityRepository,
        AnalogRepository $analogRepository,
        LatitudeRepository $latitudeRepository,
    ) {
        $this->temperatureRepository = $temperatureRepository;
        $this->humidityRepository = $humidityRepository;
        $this->analogRepository = $analogRepository;
        $this->latitudeRepository = $latitudeRepository;
    }

    public function getSensorReadingTypeRepository(string $sensorType): ReadingTypeRepositoryInterface
    {
        return match ($sensorType) {
            Temperature::getReadingTypeName() => $this->temperatureRepository,
            Humidity::getReadingTypeName() => $this->humidityRepository,
            Analog::getReadingTypeName() => $this->analogRepository,
            Latitude::getReadingTypeName() => $this->latitudeRepository,
            default => throw new SensorReadingTypeRepositoryFactoryException(
                sprintf(
                    SensorReadingTypeRepositoryFactoryException::READING_TYPE_NOT_FOUND,
                    $sensorType
                )
            )
        };
    }
}
