<?php

namespace App\Factories\Sensor\SensorReadingType;

use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Repository\Sensor\ReadingType\ORM\AnalogRepository;
use App\Repository\Sensor\ReadingType\ORM\HumidityRepository;
use App\Repository\Sensor\ReadingType\ORM\LatitudeRepository;
use App\Repository\Sensor\ReadingType\ORM\MotionRepository;
use App\Repository\Sensor\ReadingType\ORM\RelayRepository;
use App\Repository\Sensor\ReadingType\ORM\TemperatureRepository;
use App\Repository\Sensor\ReadingType\ReadingTypeRepositoryInterface;

class SensorReadingTypeRepositoryFactory
{
    private TemperatureRepository $temperatureRepository;

    private HumidityRepository $humidityRepository;

    private AnalogRepository $analogRepository;

    private LatitudeRepository $latitudeRepository;

    private RelayRepository $relayRepository;

    private MotionRepository $motionRepository;

    public function __construct(
        TemperatureRepository $temperatureRepository,
        HumidityRepository $humidityRepository,
        AnalogRepository $analogRepository,
        LatitudeRepository $latitudeRepository,
        RelayRepository $relayRepository,
        MotionRepository $motionRepository
    ) {
        $this->temperatureRepository = $temperatureRepository;
        $this->humidityRepository = $humidityRepository;
        $this->analogRepository = $analogRepository;
        $this->latitudeRepository = $latitudeRepository;
        $this->relayRepository = $relayRepository;
        $this->motionRepository = $motionRepository;
    }

    /**
     * @throws SensorReadingTypeRepositoryFactoryException
     */
    public function getSensorReadingTypeRepository(string $sensorType): ReadingTypeRepositoryInterface
    {
        return match ($sensorType) {
            Temperature::getReadingTypeName() => $this->temperatureRepository,
            Humidity::getReadingTypeName() => $this->humidityRepository,
            Analog::getReadingTypeName() => $this->analogRepository,
            Latitude::getReadingTypeName() => $this->latitudeRepository,
            Relay::getReadingTypeName() => $this->relayRepository,
            Motion::getReadingTypeName() => $this->motionRepository,
            default => throw new SensorReadingTypeRepositoryFactoryException(
                sprintf(
                    SensorReadingTypeRepositoryFactoryException::READING_TYPE_NOT_FOUND,
                    $sensorType
                )
            )
        };
    }
}
