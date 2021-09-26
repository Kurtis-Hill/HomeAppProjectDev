<?php

namespace App\Services\ESPDeviceSensor\SensorData\ConstantlyRecord;

use App\Entity\Sensors\ConstantRecording\ConstantlyRecordInterface;
use App\Entity\Sensors\SensorType;
use App\Exceptions\ConstRecordEntityException;
use App\Exceptions\OutOfBoundsEntityException;
use App\Exceptions\ReadingTypeNotSupportedException;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
use App\Services\ESPDeviceSensor\SensorData\Repository\ORM\ConstTempORMRepository;

class SensorConstantlyRecordServiceService implements SensorConstantlyRecordServiceInterface
{
    private ConstTempORMRepository $constTempORMRepository;

    public function __construct(ConstTempORMRepository $constTempORMRepository)
    {
        $this->constTempORMRepository = $constTempORMRepository;
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
                                OutOfBoundsEntityException::OUT_OF_BOUNDS_ENTITY_NOT_FOUND_MESSAGE,
                                $readingType->getSensorID()
                            )
                        );
                    }
                    $sensorConstRecordObject->setSensorReadingTypeID($readingType);
                    $sensorConstRecordObject->setSensorReading($readingType->getCurrentReading());
                    $sensorConstRecordObject->setTime();

                    $this->constTempORMRepository->persistUpdateReadingData($sensorConstRecordObject);
                }
            }

            throw new ReadingTypeNotSupportedException(
                sprintf(
                    ReadingTypeNotSupportedException::READEING_TYPE_NOT_SUPPORTED_FOR_THIS_SENSOR_MESSAGE,
                    $readingType->getSensorTypeName()
                )
            );
        }
    }
}
