<?php

namespace App\Sensors\Factories\SensorReadingType;

use App\Sensors\Builders\SensorReadingTypeResponseBuilders\AnalogResponseDTOBuilder;
use App\Sensors\Builders\SensorReadingTypeResponseBuilders\HumidityResponseDTOBuilder;
use App\Sensors\Builders\SensorReadingTypeResponseBuilders\LatitudeResponseDTOBuilder;
use App\Sensors\Builders\SensorReadingTypeResponseBuilders\StandardSensorResponseDTOBuilderInterface;
use App\Sensors\Builders\SensorReadingTypeResponseBuilders\TemperatureResponseDTOBuilder;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;

class SensorReadingTypeResponseFactory
{
    private AnalogResponseDTOBuilder $analogResponseDTOBuilder;

    private HumidityResponseDTOBuilder $humidityResponseBuilder;

    private TemperatureResponseDTOBuilder $temperatureResponseBuilder;

    private LatitudeResponseDTOBuilder $latitudeResponseBuilder;

    public function __construct(
        AnalogResponseDTOBuilder $analogResponseDTOBuilder,
        HumidityResponseDTOBuilder $humidityResponseBuilder,
        TemperatureResponseDTOBuilder $temperatureResponseBuilder,
        LatitudeResponseDTOBuilder $latitudeResponseBuilder,
    ) {
        $this->analogResponseDTOBuilder = $analogResponseDTOBuilder;
        $this->humidityResponseBuilder = $humidityResponseBuilder;
        $this->temperatureResponseBuilder = $temperatureResponseBuilder;
        $this->latitudeResponseBuilder = $latitudeResponseBuilder;
    }

    /**
     * @throws SensorReadingTypeRepositoryFactoryException|SensorReadingTypeObjectNotFoundException
     */
    public function getSensorReadingTypeDTOResponseBuilder(string $readingType): StandardSensorResponseDTOBuilderInterface
    {
        return match ($readingType) {
            Analog::getReadingTypeName() => $this->analogResponseDTOBuilder,
            Humidity::getReadingTypeName() => $this->humidityResponseBuilder,
            Temperature::getReadingTypeName() => $this->temperatureResponseBuilder,
            Latitude::READING_TYPE => $this->latitudeResponseBuilder,
            default => throw new SensorReadingTypeObjectNotFoundException(
                sprintf(
                    SensorReadingTypeRepositoryFactoryException::READING_TYPE_NOT_FOUND,
                    $readingType
                )
            )
        };
    }
}
