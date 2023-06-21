<?php

namespace App\Sensors\Repository\SensorReadingType;

use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @method ReadingTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReadingTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReadingTypes[]    findAll()
 * @method ReadingTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface ReadingTypeRepositoryInterface
{
    #[ArrayShape([ReadingTypes::class])]
    public function findAllPaginatedResults(int $limit, int $offset): array;
}
