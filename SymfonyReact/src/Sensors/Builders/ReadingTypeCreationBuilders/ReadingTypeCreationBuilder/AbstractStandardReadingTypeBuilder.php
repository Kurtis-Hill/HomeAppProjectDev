<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Sensors\Repository\ReadingType\ORM\BaseSensorReadingTypeRepository;
use Doctrine\ORM\OptimisticLockException;

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

    /**
     * @throws OptimisticLockException
     */
    protected function setBaseReadingTypeForStandardSensor(StandardReadingSensorInterface $standardSensorType, Sensor $sensor): void
    {
        $baseReadingType = $this->createNewBaseReadingTypeObject($sensor);
        $standardSensorType->setBaseReadingType($baseReadingType);
    }
}
