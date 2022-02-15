<?php

namespace App\ESPDeviceSensor\SensorDataServices\ConstantlyRecord;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Factories\ORMFactories\ConstRecord\ORMConstRecordFactoryInterface;
use App\ESPDeviceSensor\Factories\ReadingTypeFactories\ConstRecordCreationFactory;

class ConstRecordReadingTypeService implements SensorConstantlyRecordServiceInterface
{
    private ConstRecordCreationFactory $constRecordCreationFactory;

    private ORMConstRecordFactoryInterface $ormConstRecordFactory;

    public function __construct(
        ConstRecordCreationFactory $constRecordCreationFactory,
        ORMConstRecordFactoryInterface $ormConstRecordFactory,
    )
    {
        $this->constRecordCreationFactory = $constRecordCreationFactory;
        $this->ormConstRecordFactory = $ormConstRecordFactory;
    }


    public function checkAndProcessConstRecord(AllSensorReadingTypeInterface $readingTypeObject): void
    {
        if ($readingTypeObject->getConstRecord() === true) {
            $readingType = $readingTypeObject->getReadingType();

            $constRecordObjectBuilder = $this->constRecordCreationFactory->getConstRecordObjectBuilder($readingType);
            $constRecordObject = $constRecordObjectBuilder->buildConstRecordObject();

            $constRecordRepository = $this->ormConstRecordFactory->getConstRecordServiceRepository($readingType);
            $constRecordRepository->persist($constRecordObject);
        }
    }

}
