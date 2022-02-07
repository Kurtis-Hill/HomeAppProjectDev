<?php

namespace App\ESPDeviceSensor\Repository\ORM\ReadingType;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;

interface ReadingTypeRepositoryInterface
{
    public function persist(AllSensorReadingTypeInterface $readingTypeObject): void;

    public function flush(): void;

    public function findOneById(int $id);

    /**
     * @throws ORMException
     */
    public function removeObject(AllSensorReadingTypeInterface $readingTypeObject);

    /**
     * @throws NonUniqueResultException
     */
    public function getOneBySensorNameID(int $sensorNameID);

//    public function findOneByNamr

//    public function findAllBySensorName(string $name);
}
