<?php

namespace App\Sensors\Builders\SensorReadingTypeResponseBuilders\Standard;

use App\Sensors\Builders\SensorReadingTypeResponseBuilders\SensorResponseDTOBuilderInterface;
use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\Standard\LatitudeResponseDTO;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;

class LatitudeResponseDTOBuilder implements SensorResponseDTOBuilderInterface
{
    public function buildSensorReadingTypeResponseDTO(AllSensorReadingTypeInterface $relay): AllSensorReadingTypeResponseDTOInterface
    {
        if (!$relay instanceof Latitude) {
            throw new SensorReadingTypeObjectNotFoundException(
                SensorReadingTypeObjectNotFoundException::SENSOR_READING_TYPE_OBJECT_NOT_FOUND_EXCEPTION
            );
        }
        return new LatitudeResponseDTO(
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
