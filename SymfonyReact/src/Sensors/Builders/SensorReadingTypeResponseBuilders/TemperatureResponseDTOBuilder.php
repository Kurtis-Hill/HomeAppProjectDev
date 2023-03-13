<?php

namespace App\Sensors\Builders\SensorReadingTypeResponseBuilders;

use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\StandardReadingTypeResponseInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\TemperatureResponseDTO;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\Temperature;

class TemperatureResponseDTOBuilder implements StandardSensorResponseDTOBuilderInterface
{
    public function buildSensorReadingTypeResponseDTO(StandardReadingSensorInterface $analog): StandardReadingTypeResponseInterface
    {
        return new TemperatureResponseDTO(
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
