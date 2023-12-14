<?php

namespace App\Sensors\SensorServices\SensorFilter;

use App\Sensors\DTO\Internal\Sensor\SensorFilterDTO;
use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use App\Sensors\Factories\ReadingTypeQueryBuilderFactory\ReadingTypeQueryFactory;
use App\Sensors\Factories\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\Sensors\Repository\SensorReadingType\ReadingTypeRepositoryInterface;
use App\Sensors\Repository\Sensors\SensorTypeRepositoryInterface;
use App\UserInterface\Builders\CardDataQueryDTOBuilders\CardDataQueryEncapsulationDTOBuilder;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use App\UserInterface\Exceptions\ReadingTypeBuilderFailureException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
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
