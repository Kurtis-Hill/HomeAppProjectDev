<?php

namespace App\Sensors\Builders\SensorReadingTypeResponseBuilders;

use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\LatitudeResponseDTO;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\StandardReadingTypeResponseInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;

class LatitudeResponseDTOBuilder implements StandardSensorResponseDTOBuilderInterface
{
    public function buildSensorReadingTypeResponseDTO(StandardReadingSensorInterface $analog): StandardReadingTypeResponseInterface
    {
        return new LatitudeResponseDTO(
            $analog->getSensorID(),
            SensorResponseDTOBuilder::buildFullResponseDTO($analog->getSensor()),
            $analog->getCurrentReading(),
            $analog->getHighReading(),
            $analog->getLowReading(),
            $analog->getConstRecord(),
            $analog->getUpdatedAt()->format('d/m/y H:i:s'),
        );
    }
}
