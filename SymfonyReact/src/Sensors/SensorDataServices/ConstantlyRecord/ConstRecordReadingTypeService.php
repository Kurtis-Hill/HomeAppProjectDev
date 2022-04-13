<?php

namespace App\Sensors\SensorDataServices\ConstantlyRecord;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Factories\ORMFactories\ConstRecord\ORMConstRecordFactoryInterface;
use App\Sensors\Factories\ReadingTypeFactories\ConstRecordCreationFactory;

class ConstRecordReadingTypeService implements SensorConstantlyRecordServiceInterface
{
    private ConstRecordCreationFactory $constRecordCreationFactory;

    private ORMConstRecordFactoryInterface $ormConstRecordFactory;

    public function __construct(
        ConstRecordCreationFactory $constRecordCreationFactory,
        ORMConstRecordFactoryInterface $ormConstRecordFactory,
    ) {
        $this->constRecordCreationFactory = $constRecordCreationFactory;
        $this->ormConstRecordFactory = $ormConstRecordFactory;
    }

    public function checkAndProcessConstRecord(AllSensorReadingTypeInterface $readingTypeObject): void
    {
        if ($readingTypeObject->getConstRecord() === true) {
            $readingType = $readingTypeObject->getReadingType();

            $constRecordObjectBuilder = $this->constRecordCreationFactory->getConstRecordObjectBuilder($readingType);
            $constRecordObject = $constRecordObjectBuilder->buildConstRecordObject($readingTypeObject);

            $constRecordRepository = $this->ormConstRecordFactory->getConstRecordServiceRepository($readingType);
            $constRecordRepository->persist($constRecordObject);
        }
    }
}
