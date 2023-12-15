<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Entity\SensorTypes\StandardSensorTypeInterface;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Sensors\Repository\ReadingType\ORM\BaseSensorReadingTypeRepository;

abstract class AbstractStandardReadingTypeBuilder extends AbstractReadingTypeBuilder
{
    protected SensorReadingTypeRepositoryFactory $sensorReadingTypeRepositoryFactory;

    protected BaseReadingTypeBuilder $baseReadingTypeBuilder;

    public function __construct(
        SensorReadingTypeRepositoryFactory $readingTypeFactory,
        BaseReadingTypeBuilder $baseSensorReadingType,
        BaseSensorReadingTypeRepository $baseSensorReadingTypeRepository
    ) {
        $this->sensorReadingTypeRepositoryFactory = $readingTypeFactory;
        parent::__construct(
            $baseSensorReadingType,
            $baseSensorReadingTypeRepository
        );
    }

    protected function setBaseReadingTypeForStandardSensor(StandardReadingSensorInterface $standardSensorType): void
    {
        $baseReadingType = $this->createNewBaseReadingTypeObject();
        $standardSensorType->setBaseReadingType($baseReadingType);
    }
}
