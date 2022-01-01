<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\ESPDeviceSensor\DTO\SensorReadingTypeObjects\SensorReadingTypeObjectsDTO;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\Factories\CardQueryBuilderFactories\ReadingTypeQueryFactory;
use JetBrains\PhpStorm\ArrayShape;

class UpdateSensorBoundaryReadingsService implements UpdateSensorBoundaryReadingsServiceInterface
{
    private ReadingTypeQueryFactory $readingTypeQueryFactory;

    public function __construct(ReadingTypeQueryFactory $readingTypeQuery)
    {
        $this->readingTypeQueryFactory = $readingTypeQuery;
    }

    #[ArrayShape([JoinQueryDTO::class])]
    public function getSensorTypeObjectToUpdateQueryDTO(SensorReadingTypeObjectsDTO $sensorReadingTypeObjectsDTO): array
    {
        foreach ($sensorReadingTypeObjectsDTO->getSensorReadingTypeObjects() as $sensorName => $sensorReadingTypeObject) {
            $readingTypeQueryBuilder = $this->readingTypeQueryFactory->getReadingTypeQueryDTOBuilder($sensorName);
            $readingTypeQueryDTOs[] = $readingTypeQueryBuilder->buildReadingTypeJoinQueryDTO();
        }

        return $readingTypeQueryDTOs ?? [];
    }
}
