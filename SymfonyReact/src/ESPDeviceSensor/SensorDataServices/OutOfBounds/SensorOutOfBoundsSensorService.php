<?php

namespace App\ESPDeviceSensor\SensorDataServices\OutOfBounds;

use App\ESPDeviceSensor\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Exceptions\OutOfBoundsEntityException;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotSupportedException;
use App\ESPDeviceSensor\Factories\ORMFactories\OufOfBounds\OutOfBoundsFactoryInterface;

class SensorOutOfBoundsSensorService implements OutOfBoundsSensorServiceInterface
{
    private OutOfBoundsFactoryInterface $outOfBoundsFactory;

    public function __construct(OutOfBoundsFactoryInterface $outOfBoundsFactory)
    {
        $this->outOfBoundsFactory = $outOfBoundsFactory;
    }

    /**
     * @param AllSensorReadingTypeInterface $readingType
     * @return void
     * @throws OutOfBoundsEntityException
     * @throws ReadingTypeNotSupportedException
     */
    public function checkAndHandleSensorReadingOutOfBounds(AllSensorReadingTypeInterface $readingType): void
    {
        if ($readingType->isReadingOutOfBounds()) {
            foreach (SensorType::SENSOR_READING_TYPE_DATA as $sensorReadingTypeData) {
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

                    $outOfBoundsRepository = $this->outOfBoundsFactory->getOutOfBoundsServiceRepository($sensorReadingTypeData['object']);
                    $outOfBoundsRepository->persist($sensorOutOfBoundsObject);
                    $outOfBoundsRepository->flush();
                    $processed = true;

                    break;
                }
            }

            $processed ??
                throw new ReadingTypeNotSupportedException(
                    sprintf(
                        ReadingTypeNotSupportedException::READEING_TYPE_NOT_SUPPORTED_FOR_THIS_SENSOR_MESSAGE,
                        $readingType->getSensorTypeName()
                    )
                );
        }
    }
}
