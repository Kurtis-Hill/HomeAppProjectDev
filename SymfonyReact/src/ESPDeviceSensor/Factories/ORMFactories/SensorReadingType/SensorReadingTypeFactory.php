<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType;

use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\ESPDeviceSensor\Exceptions\SensorReadingTypeValidationException;
use App\ESPDeviceSensor\Repository\ORM\ReadingType\AnalogRepository;
use App\ESPDeviceSensor\Repository\ORM\ReadingType\HumidityRepository;
use App\ESPDeviceSensor\Repository\ORM\ReadingType\LatitudeRepository;
use App\ESPDeviceSensor\Repository\ORM\ReadingType\ReadingTypeRepositoryInterface;
use App\ESPDeviceSensor\Repository\ORM\ReadingType\TemperatureRepository;

class SensorReadingTypeFactory implements SensorReadingTypeFactoryInterface
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
                    $sensorType)
            )
        };
    }
}
