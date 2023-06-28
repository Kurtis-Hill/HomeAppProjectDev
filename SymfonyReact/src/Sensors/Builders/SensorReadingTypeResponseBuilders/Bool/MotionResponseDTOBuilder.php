<?php

namespace App\Sensors\Builders\SensorReadingTypeResponseBuilders\Bool;

use App\Sensors\Builders\SensorReadingTypeResponseBuilders\SensorResponseDTOBuilderInterface;
use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\Bool\MotionResponseDTO;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;

class MotionResponseDTOBuilder implements SensorResponseDTOBuilderInterface
{
    /**
     * @throws SensorReadingTypeObjectNotFoundException
     */
    public function buildSensorReadingTypeResponseDTO(AllSensorReadingTypeInterface $relay) : AllSensorReadingTypeResponseDTOInterface
    {
        if (!$relay instanceof Motion) {
            throw new SensorReadingTypeObjectNotFoundException(
                SensorReadingTypeObjectNotFoundException::SENSOR_READING_TYPE_OBJECT_NOT_FOUND_EXCEPTION
            );
        }

        return new MotionResponseDTO(
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
