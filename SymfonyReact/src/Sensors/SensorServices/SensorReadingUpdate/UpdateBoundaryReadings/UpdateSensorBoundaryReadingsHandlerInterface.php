<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\Sensors\DTO\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorReadingUpdateFactoryException;
use Doctrine\ORM\NonUniqueResultException;
use JetBrains\PhpStorm\ArrayShape;

interface UpdateSensorBoundaryReadingsHandlerInterface
{
    /**
     * @throws SensorReadingUpdateFactoryException
     * @throws ReadingTypeNotExpectedException
     */
    #[ArrayShape(["errors"])]
    public function processBoundaryDataDTO(
        SensorUpdateBoundaryDataDTOInterface $updateBoundaryDataDTO,
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
        string $sensorType,
    ): array;

    /**
     * @throws NonUniqueResultException
     * @throws SensorReadingTypeRepositoryFactoryException
     * @throws SensorReadingTypeObjectNotFoundException
     */
    public function getSensorReadingTypeObject(int $sensorID, string $readingType): AllSensorReadingTypeInterface;
}
