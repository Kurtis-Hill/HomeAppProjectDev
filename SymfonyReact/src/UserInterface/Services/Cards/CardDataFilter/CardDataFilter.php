<?php

namespace App\UserInterface\Services\Cards\CardDataFilter;

use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use App\Sensors\Factories\ReadingTypeQueryBuilderFactory\ReadingTypeQueryFactory;
use App\Sensors\Factories\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\Sensors\Repository\ORM\SensorReadingType\ReadingTypeRepositoryInterface;
use App\Sensors\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use App\UserInterface\Builders\CardDataQueryDTOBuilders\CardDataQueryEncapsulationDTOBuilder;
use App\UserInterface\DTO\Internal\CardDataFiltersDTO\CardDataPreFilterDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use App\UserInterface\Exceptions\ReadingTypeBuilderFailureException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class CardDataFilter
{
    private SensorTypeRepositoryInterface $sensorTypeRepository;

    private ReadingTypeRepositoryInterface $readingTypeRepository;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;

    private ReadingTypeQueryFactory $readingTypeQueryFactory;

    public function __construct(
        SensorTypeRepositoryInterface $sensorTypeRepository,
        ReadingTypeRepositoryInterface $readingTypeRepository,
        SensorTypeQueryFactory $sensorTypeQueryFactory,
        ReadingTypeQueryFactory $readingTypeQueryFactory,
    ) {
        $this->sensorTypeRepository = $sensorTypeRepository;
        $this->readingTypeRepository = $readingTypeRepository;
        $this->sensorTypeQueryFactory = $sensorTypeQueryFactory;
        $this->readingTypeQueryFactory = $readingTypeQueryFactory;
    }

    public function filterSensorsToQuery(CardDataPreFilterDTO $cardFilters): CardDataQueryEncapsulationFilterDTO
    {
        $allSensorTypes = $this->sensorTypeRepository->findAll();
        $sortedQueryTypes = $this->getSensorTypeJoinQueryDTOs(
            $allSensorTypes,
            $cardFilters->getSensorTypesToFilter()
        );

        $allReadingTypes = $this->readingTypeRepository->findAll();
        $readingTypesToQuery = $this->getReadingTypeJoinQueryDTOs(
            $allReadingTypes,
            $cardFilters->getReadingTypesToFilter()
        );

        return CardDataQueryEncapsulationDTOBuilder::buildCardDAtaQueryEncapsulationDTO(
            $sortedQueryTypes['sensorTypesToQuery'] ?? [],
            $sortedQueryTypes['sensorTypesNotToQuery'] ?? [],
            $readingTypesToQuery,
        );
    }

    #[ArrayShape(['sensorTypesToQuery' => JoinQueryDTO::class, 'sensorTypesNotToQuery' => SensorTypeNotJoinQueryDTO::class])]
    private function getSensorTypeJoinQueryDTOs(array $sensorTypes, array $sensorTypesToFilter = []): array
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

    #[ArrayShape([JoinQueryDTO::class||[]])]
    private function getReadingTypeJoinQueryDTOs(array $allReadingTypes, array $readingTypesToFilter = []): array
    {
        foreach ($allReadingTypes as $readingType) {
            if ($readingType instanceof ReadingTypes) {
                try {
                    $queryTypeBuilder = $this->readingTypeQueryFactory->getReadingTypeQueryDTOBuilder($readingType->getReadingType());
                    if (!in_array($readingType->getReadingType(), $readingTypesToFilter, true)) {
                        $readingTypesToQuery[] = $queryTypeBuilder->buildReadingTypeJoinQueryDTO();
                    }
                } catch (ReadingTypeBuilderFailureException) {
                    error_log('failed to retrieve query dto builder for' . $readingType->getReadingType());
                }
            }
        }

        return $readingTypesToQuery ?? [];
    }
}
