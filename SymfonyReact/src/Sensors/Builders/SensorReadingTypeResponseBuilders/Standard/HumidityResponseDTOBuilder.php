<?php

namespace App\Sensors\Builders\SensorReadingTypeResponseBuilders\Standard;

use App\Sensors\Builders\SensorReadingTypeResponseBuilders\SensorResponseDTOBuilderInterface;
use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\Standard\HumidityResponseDTO;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;

class HumidityResponseDTOBuilder implements SensorResponseDTOBuilderInterface
{
    public function buildSensorReadingTypeResponseDTO(AllSensorReadingTypeInterface $relay): AllSensorReadingTypeResponseDTOInterface
    {
        if (!$relay instanceof Humidity) {
            throw new SensorReadingTypeObjectNotFoundException(
                SensorReadingTypeObjectNotFoundException::SENSOR_READING_TYPE_OBJECT_NOT_FOUND_EXCEPTION
            );
        }

        return new HumidityResponseDTO(
            $relay->getSensorID(),
            SensorResponseDTOBuilder::buildSensorResponseDTO($relay->getSensor()),
            $relay->getCurrentReading(),
            $relay->getHighReading(),
            $relay->getLowReading(),
            $relay->getConstRecord(),
            $relay->getUpdatedAt()->format('d/m/y H:i:s'),
        );
    }
}
