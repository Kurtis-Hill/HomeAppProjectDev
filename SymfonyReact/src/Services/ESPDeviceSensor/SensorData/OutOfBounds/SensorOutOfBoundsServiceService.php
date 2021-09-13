<?php

namespace App\Services\ESPDeviceSensor\SensorData\OutOfBounds;

use App\Entity\Sensors\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Entity\Sensors\SensorType;
use App\Exceptions\OutOfBoundsEntityException;
use App\Exceptions\ReadingTypeNotSupportedException;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;

class SensorOutOfBoundsServiceService implements OutOfBoundsSensorServiceInterface
{
    /**
     * @param AllSensorReadingTypeInterface $readingType
     * @return OutOfBoundsEntityInterface|null
     * @throws OutOfBoundsEntityException
     * @throws ReadingTypeNotSupportedException
     */
    public function checkAndHandleSensorReadingOutOfBounds(AllSensorReadingTypeInterface $readingType): ?OutOfBoundsEntityInterface
    {
        if ($readingType->isReadingOutOfBounds()) {
            foreach (SensorType::SENSOR_READING_TYPE_DATA as $sensorReadingTypeData) {
//                dd($sensorReadingTypeData);
                if ($sensorReadingTypeData['object'] === $readingType::class) {
                    $sensorOutOfBoundsObject = new $sensorReadingTypeData['outOfBounds'];

                    if (!$sensorOutOfBoundsObject instanceof OutOfBoundsEntityInterface) {
                        throw new OutOfBoundsEntityException(
                            sprintf(
                                OutOfBoundsEntityException::OUT_OF_BOUNDS_ENTITY_NOT_FOUND_MESSAGE,
                                $readingType->getSensorID()
                            )
                        );
                    }
                    $sensorOutOfBoundsObject->setSensorReadingTypeID($readingType);
                    $sensorOutOfBoundsObject->setSensorReading($readingType->getCurrentReading());
                    $sensorOutOfBoundsObject->setTime();

                    return $sensorOutOfBoundsObject;
                }
            }

            throw new ReadingTypeNotSupportedException(
                sprintf(
                    ReadingTypeNotSupportedException::READEING_TYPE_NOT_SUPPORTED_FOR_THIS_SENSOR_MESSAGE,
                    $readingType->getSensorTypeName()
                    )
                );
            }

            return null;
        }

}
