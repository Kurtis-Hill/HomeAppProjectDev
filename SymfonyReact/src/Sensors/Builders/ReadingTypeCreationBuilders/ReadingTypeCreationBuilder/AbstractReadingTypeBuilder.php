<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
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
     * @throws ORMException|OptimisticLockException
     */
    protected function createNewBaseReadingTypeObject(): BaseSensorReadingType
    {
        $baseReadingType = $this->baseReadingTypeBuilder->buildBaseReadingTypeObject();
        $this->baseSensorReadingTypeRepository->persist($baseReadingType);
        $this->baseSensorReadingTypeRepository->flush();

        return $baseReadingType;
    }
}
