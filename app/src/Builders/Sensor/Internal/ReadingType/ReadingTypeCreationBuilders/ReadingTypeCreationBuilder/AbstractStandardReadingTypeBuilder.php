<?php
declare(strict_types=1);

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Entity\Sensor\Sensor;
use App\Factories\Sensor\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Repository\Sensor\ReadingType\ORM\BaseSensorReadingTypeRepository;
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
