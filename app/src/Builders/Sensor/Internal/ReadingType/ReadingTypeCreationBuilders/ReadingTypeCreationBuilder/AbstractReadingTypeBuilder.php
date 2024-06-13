<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\Sensor;
use App\Repository\Sensor\ReadingType\ORM\BaseSensorReadingTypeRepository;
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
