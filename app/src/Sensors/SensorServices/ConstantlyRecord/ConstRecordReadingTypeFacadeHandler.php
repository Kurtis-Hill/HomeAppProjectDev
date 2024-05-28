<?php

namespace App\Sensors\SensorServices\ConstantlyRecord;

use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Factories\ConstRecord\ConstRecordFactoryInterface;
use App\Sensors\Factories\ReadingTypeFactories\ConstRecordCreationFactory;

class ConstRecordReadingTypeFacadeHandler implements SensorConstantlyRecordHandlerInterface
{
    private ConstRecordCreationFactory $constRecordCreationFactory;

    private ConstRecordFactoryInterface $constRecordRepositoryFactory;

    public function __construct(
        ConstRecordCreationFactory $constRecordCreationFactory,
        ConstRecordFactoryInterface $constRecordRepositoryFactory,
    ) {
        $this->constRecordCreationFactory = $constRecordCreationFactory;
        $this->constRecordRepositoryFactory = $constRecordRepositoryFactory;
    }

    public function processConstRecord(AllSensorReadingTypeInterface $readingTypeObject): void
    {
        if ($readingTypeObject->getConstRecord() === true) {
            $readingType = $readingTypeObject->getReadingType();

            $constRecordObjectBuilder = $this->constRecordCreationFactory->getConstRecordObjectBuilder($readingType);
            $constRecordObject = $constRecordObjectBuilder->buildConstRecordObject($readingTypeObject);
            $constRecordRepository = $this->constRecordRepositoryFactory->getConstRecordServiceRepository($readingType);
            $constRecordRepository->persist($constRecordObject);
        }
    }
}
