<?php

namespace App\Sensors\Builders\SensorReadingTypeResponseBuilders;

use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\HumidityResponseDTO;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\StandardReadingTypeResponseInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;

class HumidityResponseDTOBuilder implements StandardSensorResponseDTOBuilderInterface
{
    public function buildSensorReadingTypeResponseDTO(StandardReadingSensorInterface $analog): StandardReadingTypeResponseInterface
    {
        return new HumidityResponseDTO(
            $analog->getSensorID(),
            SensorResponseDTOBuilder::buildDetailedResponseDTO($analog->getSensor()),
            $analog->getCurrentReading(),
            $analog->getHighReading(),
            $analog->getLowReading(),
            $analog->getConstRecord(),
            $analog->getUpdatedAt()->format('d/m/y H:i:s'),
        );
    }
}
