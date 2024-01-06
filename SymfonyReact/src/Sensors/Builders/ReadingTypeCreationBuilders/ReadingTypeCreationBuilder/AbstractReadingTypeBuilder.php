<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\ReadingType\ORM\BaseSensorReadingTypeRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

abstract class AbstractReadingTypeBuilder
{
    protected BaseReadingTypeBuilder $baseReadingTypeBuilder;

    private BaseSensorReadingTypeRepository $baseSensorReadingTypeRepository;

    public function __construct(BaseReadingTypeBuilder $baseSensorReadingType, BaseSensorReadingTypeRepository $baseSensorReadingTypeRepository)
    {
        $this->baseReadingTypeBuilder = $baseSensorReadingType;
        $this->baseSensorReadingTypeRepository = $baseSensorReadingTypeRepository;
    }

    /**
     * @param Sensor $sensor
     * @return BaseSensorReadingType
     * @throws OptimisticLockException
     * @throws ORMException
     */
    protected function createNewBaseReadingTypeObject(Sensor $sensor): BaseSensorReadingType
    {
        $baseReadingType = $this->baseReadingTypeBuilder->buildBaseReadingTypeObject($sensor);
        $this->baseSensorReadingTypeRepository->persist($baseReadingType);
        $this->baseSensorReadingTypeRepository->flush();

        return $baseReadingType;
    }
}
