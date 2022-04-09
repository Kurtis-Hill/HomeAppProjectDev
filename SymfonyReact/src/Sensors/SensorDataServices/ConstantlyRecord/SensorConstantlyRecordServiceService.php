<?php

namespace App\Sensors\SensorDataServices\ConstantlyRecord;

use App\Sensors\Entity\ConstantRecording\ConstantlyRecordInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorType;
use App\Sensors\Exceptions\ConstRecordEntityException;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Factories\ORMFactories\ConstRecord\ORMConstRecordFactoryInterface;

class SensorConstantlyRecordServiceService implements SensorConstantlyRecordServiceInterface
{
    private ORMConstRecordFactoryInterface $constORMRepositoryFactory;

    public function __construct(ORMConstRecordFactoryInterface $constORMRepositoryFactory)
    {
        $this->constORMRepositoryFactory = $constORMRepositoryFactory;
    }

    /**
     * @param AllSensorReadingTypeInterface $readingTypeObject
     * @return void
     * @throws ConstRecordEntityException
     * @throws ReadingTypeNotSupportedException
     */
    public function checkAndProcessConstRecord(AllSensorReadingTypeInterface $readingTypeObject): void
    {
        if (!$readingTypeObject->getConstRecord()) {
            foreach (SensorType::SENSOR_READING_TYPE_DATA as $sensorReadingTypeData) {
                if ($sensorReadingTypeData['object'] === $readingTypeObject::class) {
                    $sensorConstRecordObject = new $sensorReadingTypeData['constRecord'];

                    if (!$sensorConstRecordObject instanceof ConstantlyRecordInterface) {
                        throw new ConstRecordEntityException(
                            sprintf(
                                ConstRecordEntityException::CONST_RECORD_ENTITY_NOT_FOUND_MESSAGE,
                                $readingTypeObject->getSensorID()
                            )
                        );
                    }
                    $sensorConstRecordObject->setSensorReadingTypeObject($readingTypeObject);
                    $sensorConstRecordObject->setSensorReading($readingTypeObject->getCurrentReading());
                    $sensorConstRecordObject->setCreatedAt();

                    $constORMRepository = $this->constORMRepositoryFactory->getConstRecordServiceRepository($sensorReadingTypeData['object']);

                    $constORMRepository->persist($sensorConstRecordObject);
                    $constORMRepository->flush();
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
