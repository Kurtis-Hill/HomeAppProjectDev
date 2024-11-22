<?php

namespace App\Factories\Sensor\SensorReadingType;

use App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\Bool\MotionResponseDTOBuilder;
use App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\Bool\RelayResponseDTOBuilder;
use App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\SensorResponseDTOBuilderInterface;
use App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\Standard\AnalogResponseDTOBuilder;
use App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\Standard\HumidityResponseDTOBuilder;
use App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\Standard\LatitudeResponseDTOBuilder;
use App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\Standard\TemperatureResponseDTOBuilder;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Exceptions\Sensor\SensorReadingTypeObjectNotFoundException;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;

class SensorReadingTypeResponseFactory
{
    private AnalogResponseDTOBuilder $analogResponseDTOBuilder;

    private HumidityResponseDTOBuilder $humidityResponseBuilder;

    private TemperatureResponseDTOBuilder $temperatureResponseBuilder;

    private LatitudeResponseDTOBuilder $latitudeResponseBuilder;

    private MotionResponseDTOBuilder $motionResponseBuilder;

    private RelayResponseDTOBuilder $relayResponseBuilder;

    public function __construct(
        AnalogResponseDTOBuilder $analogResponseDTOBuilder,
        HumidityResponseDTOBuilder $humidityResponseBuilder,
        TemperatureResponseDTOBuilder $temperatureResponseBuilder,
        LatitudeResponseDTOBuilder $latitudeResponseBuilder,
        MotionResponseDTOBuilder $motionResponseBuilder,
        RelayResponseDTOBuilder $relayResponseBuilder
    ) {
        $this->analogResponseDTOBuilder = $analogResponseDTOBuilder;
        $this->humidityResponseBuilder = $humidityResponseBuilder;
        $this->temperatureResponseBuilder = $temperatureResponseBuilder;
        $this->latitudeResponseBuilder = $latitudeResponseBuilder;
        $this->motionResponseBuilder = $motionResponseBuilder;
        $this->relayResponseBuilder = $relayResponseBuilder;
    }

    /**
     * @throws SensorReadingTypeRepositoryFactoryException|SensorReadingTypeObjectNotFoundException
     */
    public function getSensorReadingTypeDTOResponseBuilder(string $readingType): SensorResponseDTOBuilderInterface
    {
        return match ($readingType) {
            Analog::getReadingTypeName() => $this->analogResponseDTOBuilder,
            Humidity::getReadingTypeName() => $this->humidityResponseBuilder,
            Temperature::getReadingTypeName() => $this->temperatureResponseBuilder,
            Latitude::getReadingTypeName() => $this->latitudeResponseBuilder,
            Motion::getReadingTypeName() => $this->motionResponseBuilder,
            Relay::getReadingTypeName() => $this->relayResponseBuilder,
            default => throw new SensorReadingTypeObjectNotFoundException(
                sprintf(
                    SensorReadingTypeRepositoryFactoryException::READING_TYPE_NOT_FOUND,
                    $readingType
                )
            )
        };
    }
}
