<?php

namespace App\UserInterface\Services\Cards\CardDataFilterService;

use App\ESPDeviceSensor\Entity\ReadingTypes;
use App\ESPDeviceSensor\Factories\ReadingTypeQueryBuilderFactory\ReadingTypeQueryFactory;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use App\UserInterface\DTO\CardDataFiltersDTO\CardDataPreFilterDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\UserInterface\DTO\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\Exceptions\ReadingTypeBuilderFailureException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use App\ESPDeviceSensor\Factories\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class CardDataFilterService implements CardDataFilterServiceInterface
{
    private SensorTypeRepositoryInterface $sensorTypeRepository;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;

    private ReadingTypeQueryFactory $readingTypeQueryFactory;

    public function __construct(
        SensorTypeRepositoryInterface $sensorTypeRepository,
        SensorTypeQueryFactory $sensorTypeQueryFactory,
        ReadingTypeQueryFactory $readingTypeQueryFactory,
    ) {
        $this->sensorTypeRepository = $sensorTypeRepository;
        $this->sensorTypeQueryFactory = $sensorTypeQueryFactory;
        $this->readingTypeQueryFactory = $readingTypeQueryFactory;
    }

    public function filterSensorsToQuery(CardDataPreFilterDTO $cardFilters): CardDataQueryEncapsulationFilterDTO
    {
        $allSensorTypes = $this->sensorTypeRepository->findAll();

        $sortedQueryTypes = $this->filterSensorByType($allSensorTypes, $cardFilters->getSensorTypesToFilter());
        $allReadingTypes = ReadingTypes::SENSOR_READING_TYPE_DATA;

        $readingTypesToQuery = $this->filterSensorByReadingType($allReadingTypes, $cardFilters->getReadingTypesToFilter());

        return new CardDataQueryEncapsulationFilterDTO(
            $sortedQueryTypes['sensorTypesToQuery'] ?? [],
            $sortedQueryTypes['sensorTypesNotToQuery'] ?? [],
            $readingTypesToQuery,
        );
    }

    #[Pure]
    public function preparePreFilterDTO(array $sensorTypesToFilter, array $readingTypesToFilter): CardDataPreFilterDTO
    {
        return new CardDataPreFilterDTO(
            $sensorTypesToFilter,
            $readingTypesToFilter,
        );
    }

    #[ArrayShape(['sensorTypesToQuery' => JoinQueryDTO::class, 'sensorTypesNotToQuery' => SensorTypeNotJoinQueryDTO::class])]
    private function filterSensorByType(array $sensorTypes, array $sensorTypesToFilter = []): array
    {
        foreach ($sensorTypes as $sensorType) {
            try {
                $queryTypeBuilder = $this->sensorTypeQueryFactory->getSensorTypeQueryDTOBuilder($sensorType->getSensorType());
                if (in_array($sensorType->getSensorType(), $sensorTypesToFilter, true)) {
                    $sensorTypesNotToQuery[] = $queryTypeBuilder->buildSensorTypeQueryExcludeDTO($sensorType->getSensorTypeID());
                }
                else {
                    $sensorTypesToQuery[] = $queryTypeBuilder->buildSensorTypeQueryJoinDTO();
                }
            } catch (SensorTypeBuilderFailureException) {
                error_log('failed to retrieve query dto builder for' . $sensorType->getSensorType());
            }
        }


        return [
            'sensorTypesToQuery' => $sensorTypesToQuery ?? [],
            'sensorTypesNotToQuery' => $sensorTypesNotToQuery ?? [],
        ];
    }

    private function filterSensorByReadingType(array $allReadingTypes, array $readingTypesToFilter = []): array
    {
        foreach ($allReadingTypes as $sensorReadingType => $readingType) {
            try {
                $queryTypeBuilder = $this->readingTypeQueryFactory->getReadingTypeQueryDTOBuilder($sensorReadingType);
                if (!in_array($sensorReadingType, $readingTypesToFilter, true)) {
                    $readingTypesToQuery[] = $queryTypeBuilder->buildReadingTypeJoinQueryDTO();
                }
            } catch (ReadingTypeBuilderFailureException) {
                error_log('failed to retrieve query dto builder for' . $readingType->getSensorType());
            }
        }

        return $readingTypesToQuery ?? [];
    }
}
