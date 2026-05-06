<?php

namespace App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\Standard;

use App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\SensorResponseDTOBuilderInterface;
use App\Builders\Sensor\Response\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\DTOs\Sensor\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\DTOs\Sensor\Response\SensorReadingTypeResponse\Standard\AnalogResponseDTO;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\SensorReadingTypeObjectNotFoundException;

class AnalogResponseDTOBuilder implements SensorResponseDTOBuilderInterface
{
    /**
     * @throws SensorReadingTypeObjectNotFoundException
     */
    public function buildSensorReadingTypeResponseDTO(AllSensorReadingTypeInterface $readingTypeObject): AllSensorReadingTypeResponseDTOInterface
    {
        if (!$readingTypeObject instanceof Analog) {
            throw new SensorReadingTypeObjectNotFoundException(
                SensorReadingTypeObjectNotFoundException::SENSOR_READING_TYPE_OBJECT_NOT_FOUND_EXCEPTION
            );
        }
        return new AnalogResponseDTO(
            $readingTypeObject->getReadingTypeID(),
            $readingTypeObject->getBaseReadingType()->getBaseReadingTypeID(),
            SensorResponseDTOBuilder::buildSensorResponseDTO($readingTypeObject->getSensor()),
            $readingTypeObject->getCurrentReading(),
            $readingTypeObject->getHighReading(),
            $readingTypeObject->getLowReading(),
            $readingTypeObject->getConstRecord(),
            $readingTypeObject->getUpdatedAt()->format('d/m/y H:i:s'),
        );
    }
}
