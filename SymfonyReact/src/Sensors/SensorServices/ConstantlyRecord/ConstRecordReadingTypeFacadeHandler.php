<?php

namespace App\Sensors\SensorServices\ConstantlyRecord;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Factories\ORMFactories\ConstRecord\ORMConstRecordFactory;
use App\Sensors\Factories\ReadingTypeFactories\ConstRecordCreationFactory;

class ConstRecordReadingTypeFacadeHandler implements SensorConstantlyRecordHandlerInterface
{
    private ConstRecordCreationFactory $constRecordCreationFactory;

    private ORMConstRecordFactory $ormConstRecordFactory;

    public function __construct(
        ConstRecordCreationFactory $constRecordCreationFactory,
        ORMConstRecordFactory $ormConstRecordFactory,
    ) {
        $this->constRecordCreationFactory = $constRecordCreationFactory;
        $this->ormConstRecordFactory = $ormConstRecordFactory;
    }

    public function processConstRecord(AllSensorReadingTypeInterface $readingTypeObject): void
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
