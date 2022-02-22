<?php

namespace App\ESPDeviceSensor\Repository\ORM\ReadingType;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\ReadingTypes;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\ArrayShape;

interface ReadingTypeRepositoryInterface
{
    public function persist(AllSensorReadingTypeInterface $readingTypeObject): void;

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
