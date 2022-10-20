<?php

namespace App\Sensors\SensorServices\ConstantlyRecord;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Factories\ConstRecord\ConstRecordORMFactory;
use App\Sensors\Factories\ReadingTypeFactories\ConstRecordCreationFactory;

class ConstRecordReadingTypeFacadeHandler implements SensorConstantlyRecordHandlerInterface
{
    private ConstRecordCreationFactory $constRecordCreationFactory;

    private ConstRecordORMFactory $ormConstRecordFactory;

    public function __construct(
        ConstRecordCreationFactory $constRecordCreationFactory,
        ConstRecordORMFactory $ormConstRecordFactory,
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
