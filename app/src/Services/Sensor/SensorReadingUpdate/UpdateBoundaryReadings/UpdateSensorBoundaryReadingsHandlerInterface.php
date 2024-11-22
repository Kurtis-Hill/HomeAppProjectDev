<?php

namespace App\Services\Sensor\SensorReadingUpdate\UpdateBoundaryReadings;

use App\DTOs\Sensor\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\ReadingTypeNotExpectedException;
use App\Exceptions\Sensor\SensorReadingTypeObjectNotFoundException;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Exceptions\Sensor\SensorReadingUpdateFactoryException;
use App\Exceptions\Sensor\SensorTypeNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use JetBrains\PhpStorm\ArrayShape;

interface UpdateSensorBoundaryReadingsHandlerInterface
{
    /**
     * @throws SensorReadingUpdateFactoryException
     * @throws ReadingTypeNotExpectedException
     * @throws SensorTypeNotFoundException
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
