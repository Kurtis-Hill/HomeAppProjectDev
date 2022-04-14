<?php

namespace App\Sensors\Factories\ORMFactories\OufOfBounds;

use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Repository\ORM\OutOfBounds\OutOfBoundsRepositoryInterface;

interface OutOfBoundsORMFactoryInterface
{
    /**
     * @throws ReadingTypeNotSupportedException
     */
    public function getOutOfBoundsServiceRepository(string $sensorReadingType): OutOfBoundsRepositoryInterface;
}
