<?php

namespace App\Sensors\Factories\ORMFactories\SensorReadingType;

use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Repository\ORM\ReadingType\AnalogRepository;
use App\Sensors\Repository\ORM\ReadingType\HumidityRepository;
use App\Sensors\Repository\ORM\ReadingType\LatitudeRepository;
use App\Sensors\Repository\ORM\ReadingType\ReadingTypeRepositoryInterface;
use App\Sensors\Repository\ORM\ReadingType\TemperatureRepository;

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
            Temperature::READING_TYPE => $this->temperatureRepository,
            Humidity::READING_TYPE => $this->humidityRepository,
            Analog::READING_TYPE => $this->analogRepository,
            Latitude::READING_TYPE => $this->latitudeRepository,
            default => throw new SensorReadingTypeRepositoryFactoryException(
                sprintf(
                    SensorReadingTypeRepositoryFactoryException::READING_TYPE_NOT_FOUND,
                    $sensorType
                )
            )
        };
    }
}
