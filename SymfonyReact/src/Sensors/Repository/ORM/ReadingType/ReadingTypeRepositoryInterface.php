<?php

namespace App\Sensors\Repository\ORM\ReadingType;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

interface ReadingTypeRepositoryInterface
{
    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(AllSensorReadingTypeInterface $readingTypeObject): void;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function flush(): void;

    public function findOneById(int $id);

    /**
     * @throws NonUniqueResultException
     */
    public function getOneBySensorNameID(int $sensorNameID);

    #[ArrayShape([ReadingTypes::class])]
    public function findAll();

//    public function findOneByNamr

//    public function findAllBySensorName(string $name);
}
