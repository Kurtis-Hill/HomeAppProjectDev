<?php

namespace App\Sensors\Builders\SensorReadingTypeResponseBuilders\Bool;

use App\Sensors\Builders\SensorReadingTypeResponseBuilders\SensorResponseDTOBuilderInterface;
use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\Bool\RelayResponseDTO;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;

class RelayResponseDTOBuilder implements SensorResponseDTOBuilderInterface
{
    public function buildSensorReadingTypeResponseDTO(AllSensorReadingTypeInterface $relay) : AllSensorReadingTypeResponseDTOInterface
    {
        if (!$relay instanceof Relay) {
            throw new SensorReadingTypeObjectNotFoundException(
                SensorReadingTypeObjectNotFoundException::SENSOR_READING_TYPE_OBJECT_NOT_FOUND_EXCEPTION
            );
        }

        return new RelayResponseDTO(
            sensorResponseDTO: SensorResponseDTOBuilder::buildSensorResponseDTO($relay->getSensor()),
            boolID: $relay->getBoolID(),
            currentReading: $relay->getCurrentReading(),
            requestedReading: $relay->getRequestedReading(),
            constRecord: $relay->getConstRecord(),
            updatedAt: $relay->getUpdatedAt()->format('d/m/y H:i:s'),
            expectedReading: $relay->getExpectedReading(),
        );
    }

}
