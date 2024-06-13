<?php

namespace App\Services\Sensor\SensorFilter;

use App\Builders\UserInterface\CardDataQueryDTOBuilders\CardDataQueryEncapsulationDTOBuilder;
use App\DTOs\Sensor\Internal\Sensor\SensorFilterDTO;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use App\Entity\Sensor\ReadingTypes\ReadingTypes;
use App\Exceptions\UserInterface\ReadingTypeBuilderFailureException;
use App\Exceptions\UserInterface\SensorTypeBuilderFailureException;
use App\Factories\Sensor\ReadingTypeQueryBuilderFactory\ReadingTypeQueryFactory;
use App\Factories\Sensor\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\Repository\Sensor\SensorReadingType\ReadingTypeRepositoryInterface;
use App\Repository\Sensor\Sensors\SensorTypeRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Log\LoggerInterface;

class SensorFilter
{
    private SensorTypeRepositoryInterface $sensorTypeRepository;

    private ReadingTypeRepositoryInterface $readingTypeRepository;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;

    private ReadingTypeQueryFactory $readingTypeQueryFactory;

    private LoggerInterface $logger;

    public function __construct(
        SensorTypeRepositoryInterface $sensorTypeRepository,
        ReadingTypeRepositoryInterface $readingTypeRepository,
        SensorTypeQueryFactory $sensorTypeQueryFactory,
        ReadingTypeQueryFactory $readingTypeQueryFactory,
        LoggerInterface $logger,
    ) {
        $this->sensorTypeRepository = $sensorTypeRepository;
        $this->readingTypeRepository = $readingTypeRepository;
        $this->sensorTypeQueryFactory = $sensorTypeQueryFactory;
        $this->readingTypeQueryFactory = $readingTypeQueryFactory;
        $this->logger = $logger;
    }

    public function filterSensorsToQuery(SensorFilterDTO $cardFilters): CardDataQueryEncapsulationFilterDTO
    {
        $sortedQueryTypes = $this->createSensorTypeJoinQueryDTOs(
            $cardFilters->getSensorTypesToFilter()
        );

        $readingTypesToQuery = $this->createReadingTypeJoinQueryDTOs(
            $cardFilters->getReadingTypesToFilter()
        );

        return CardDataQueryEncapsulationDTOBuilder::buildCardDAtaQueryEncapsulationDTO(
            $sortedQueryTypes['sensorTypesToQuery'] ?? [],
            $sortedQueryTypes['sensorTypesNotToQuery'] ?? [],
            $readingTypesToQuery,
        );
    }

    #[ArrayShape(['sensorTypesToQuery' => JoinQueryDTO::class, 'sensorTypesNotToQuery' => SensorTypeNotJoinQueryDTO::class])]
    private function createSensorTypeJoinQueryDTOs(array $sensorTypesToFilterOut = []): array
    {
        $allSensorTypes = $this->sensorTypeRepository->findAll();
        foreach ($allSensorTypes as $sensorType) {
            try {
                $queryTypeBuilder = $this->sensorTypeQueryFactory->getSensorTypeQueryDTOBuilder($sensorType::getReadingTypeName());
                if (in_array($sensorType::getReadingTypeName(), $sensorTypesToFilterOut, true)) {
                    $sensorTypesNotToQuery[] = $queryTypeBuilder->buildSensorTypeQueryExcludeDTO($sensorType->getSensorTypeID());
                } else {
                    $sensorTypesToQuery[] = $queryTypeBuilder->buildSensorTypeQueryJoinDTO();
                }
            } catch (SensorTypeBuilderFailureException) {
                $this->logger->error(
                    'failed to retrieve query dto builder for' . $sensorType::getReadingTypeName()
                );
            }
        }

        return [
            'sensorTypesToQuery' => $sensorTypesToQuery ?? [],
            'sensorTypesNotToQuery' => $sensorTypesNotToQuery ?? [],
        ];
    }

    #[ArrayShape([JoinQueryDTO::class||[]])]
    private function createReadingTypeJoinQueryDTOs(array $readingTypesToFilter = []): array
    {
        $allReadingTypes = $this->readingTypeRepository->findAll();
        foreach ($allReadingTypes as $readingType) {
            if ($readingType instanceof ReadingTypes) {
                try {
                    $queryTypeBuilder = $this->readingTypeQueryFactory->getReadingTypeQueryDTOBuilder($readingType->getReadingType());
                    if (!in_array($readingType->getReadingType(), $readingTypesToFilter, true)) {
                        $readingTypesToQuery[] = $queryTypeBuilder->buildReadingTypeJoinQueryDTO();
                    }
                } catch (ReadingTypeBuilderFailureException) {
                    $this->logger->error(
                        'failed to retrieve query dto builder for' . $readingType->getReadingType()
                    );
                }
            }
        }

        return $readingTypesToQuery ?? [];
    }
}
