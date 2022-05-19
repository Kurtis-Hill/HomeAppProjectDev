<?php

namespace App\Sensors\SensorDataServices\OutOfBounds;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Entity\SensorType;
use App\Sensors\Exceptions\OutOfBoundsEntityException;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Factories\ORMFactories\OufOfBounds\OutOfBoundsORMFactoryInterface;

class SensorOutOfBoundsSensorService implements OutOfBoundsSensorServiceInterface
{
    private OutOfBoundsORMFactoryInterface $outOfBoundsFactory;

    public function __construct(OutOfBoundsORMFactoryInterface $outOfBoundsFactory)
    {
        $this->outOfBoundsFactory = $outOfBoundsFactory;
    }

    public function checkAndProcessOutOfBounds(StandardReadingSensorInterface $readingTypeObject): void
    {
        if ($readingTypeObject->isReadingOutOfBounds()) {
            foreach (SensorType::SENSOR_READING_TYPE_DATA as $sensorReadingTypeData) {
                if ($sensorReadingTypeData['object'] === $readingTypeObject::class) {
                    $sensorOutOfBoundsObject = new $sensorReadingTypeData['outOfBounds'];

                    if (!$sensorOutOfBoundsObject instanceof OutOfBoundsEntityInterface) {
                        throw new OutOfBoundsEntityException(
                            sprintf(
                                OutOfBoundsEntityException::OUT_OF_BOUNDS_ENTITY_NOT_FOUND_MESSAGE,
                                $readingTypeObject->getSensorID()
                            )
                        );
                    }
                    $sensorOutOfBoundsObject->setSensorReadingTypeID($readingTypeObject);
                    $sensorOutOfBoundsObject->setSensorReading($readingTypeObject->getCurrentReading());
                    $sensorOutOfBoundsObject->setCreatedAt();

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
                        ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_FOR_THIS_SENSOR_MESSAGE,
                        $readingTypeObject->getReadingType()
                    )
                );
        }
    }
}
