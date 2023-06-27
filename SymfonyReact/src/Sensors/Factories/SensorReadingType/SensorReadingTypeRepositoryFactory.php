<?php

namespace App\Sensors\Factories\SensorReadingType;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Repository\ReadingType\ORM\AnalogRepository;
use App\Sensors\Repository\ReadingType\ORM\HumidityRepository;
use App\Sensors\Repository\ReadingType\ORM\LatitudeRepository;
use App\Sensors\Repository\ReadingType\ORM\MotionRepository;
use App\Sensors\Repository\ReadingType\ORM\RelayRepository;
use App\Sensors\Repository\ReadingType\ORM\TemperatureRepository;
use App\Sensors\Repository\ReadingType\ReadingTypeRepositoryInterface;

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
