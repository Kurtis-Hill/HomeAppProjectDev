<?php

namespace App\ESPDeviceSensor\SensorDataServices\ConstantlyRecord;

use App\ESPDeviceSensor\Entity\ConstantRecording\ConstantlyRecordInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Exceptions\ConstRecordEntityException;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotSupportedException;
use App\ESPDeviceSensor\Factories\ORMFactories\ConstRecord\ORMConstRecordFactoryInterface;

class SensorConstantlyRecordServiceService implements SensorConstantlyRecordServiceInterface
{
    private ORMConstRecordFactoryInterface $constORMRepositoryFactory;

    public function __construct(ORMConstRecordFactoryInterface $constORMRepositoryFactory)
    {
        $this->constORMRepositoryFactory = $constORMRepositoryFactory;
    }

    /**
     * @param AllSensorReadingTypeInterface $readingType
     * @return void
     * @throws ConstRecordEntityException
     * @throws ReadingTypeNotSupportedException
     */
    public function checkAndProcessConstRecord(AllSensorReadingTypeInterface $readingType): void
    {
        if (!$readingType->getConstRecord()) {
            foreach (SensorType::SENSOR_READING_TYPE_DATA as $sensorReadingTypeData) {
                if ($sensorReadingTypeData['object'] === $readingType::class) {
                    $sensorConstRecordObject = new $sensorReadingTypeData['constRecord'];

                    if (!$sensorConstRecordObject instanceof ConstantlyRecordInterface) {
                        throw new ConstRecordEntityException(
                            sprintf(
                                ConstRecordEntityException::CONST_RECORD_ENTITY_NOT_FOUND_MESSAGE,
                                $readingType->getSensorID()
                            )
                        );
                    }
                    $sensorConstRecordObject->setSensorReadingTypeID($readingType);
                    $sensorConstRecordObject->setSensorReading($readingType->getCurrentReading());
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
                        $readingType->getReadingType()
                        )
                    );
        }
    }
}
