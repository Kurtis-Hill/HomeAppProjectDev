<?php

namespace App\ESPDeviceSensor\SensorDataServices\ConstantlyRecord;

use App\Entity\Sensors\ConstantRecording\ConstantlyRecordInterface;
use App\Entity\Sensors\SensorType;
use App\ESPDeviceSensor\Factories\ORMFactories\ConstRecord\ORMConstRecordFactoryInterface;
use App\Exceptions\ConstRecordEntityException;
use App\Exceptions\OutOfBoundsEntityException;
use App\Exceptions\ReadingTypeNotSupportedException;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;

class SensorConstantlyRecordServiceService implements SensorConstantlyRecordServiceInterface
{
    private ORMConstRecordFactoryInterface $constTempORMRepository;

    public function __construct(ORMConstRecordFactoryInterface $constORMRepository)
    {
        $this->constTempORMRepository = $constORMRepository;
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
//            dd('con');
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

                    $constORMRepository = $this->constTempORMRepository->getConstRecordServiceRepository($sensorReadingTypeData['object']);

                    $constORMRepository->persist($sensorConstRecordObject);
                    $constORMRepository->flush();
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
