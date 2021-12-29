<?php

namespace App\UserInterface\Services\Cards\CardDataFilterService;

use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Exceptions\SensorTypeBuilderFailureException;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use App\UserInterface\DTO\CardDataFiltersDTO\CardDataPreFilterDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeJoinQueryDTO;
use App\UserInterface\Factories\CardQueryBuilderFactories\SensorTypeQueryFactory;
use JetBrains\PhpStorm\ArrayShape;

class CardDataFilterService implements CardDataFilterServiceInterface
{
    private SensorTypeRepositoryInterface $sensorTypeRepository;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;

    public function __construct(
        SensorTypeRepositoryInterface $sensorTypeRepository,
        SensorTypeQueryFactory $sensorTypeQueryFactory,
    ) {
        $this->sensorTypeRepository = $sensorTypeRepository;
        $this->sensorTypeQueryFactory = $sensorTypeQueryFactory;
    }

    public function filterSensorsToQuery(CardDataPreFilterDTO $cardFilters): CardDataQueryEncapsulationFilterDTO
    {
        $allSensorTypes = $this->sensorTypeRepository->findAll();

        $sortedQueryTypes = $this->filterSensorByType($allSensorTypes, $cardFilters->getSensorTypesToFilter());
        $allReadingTypes = array_keys(SensorType::SENSOR_READING_TYPE_DATA);

        $readingTypesToQuery = $this->filterSensorByReadingType($allReadingTypes, $cardFilters->getReadingTypesToFilter());
        return new CardDataQueryEncapsulationFilterDTO(
            $sortedQueryTypes['sensorTypesToQuery'] ?? [],
            $sortedQueryTypes['sensorTypesNotToQuery'] ?? [],
            $readingTypesToQuery,
        );
    }

    #[ArrayShape([CardSensorTypeJoinQueryDTO::class])]
    private function filterSensorByType(array $sensorTypes, array $sensorTypeIdsToFilter = []): array
    {
        foreach ($sensorTypes as $sensorType) {
            try {
                $queryTypeBuilder = $this->sensorTypeQueryFactory->getSensorTypeQueryDTOBuilder($sensorType->getSensorType());
                if (in_array($sensorType->getSensorType(), $sensorTypeIdsToFilter, true)) {
                    $sensorTypesNotToQuery[] = $queryTypeBuilder->buildSensorTypeQueryExcludeSensorDTO($sensorType->getSensorTypeID());
                }
                else {
                    $sensorTypesToQuery[] = $queryTypeBuilder->buildSensorTypeQueryDTOSensorNameJoin();
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


        // make new table for different sensor types
    private function filterSensorByReadingType(array $allReadingTypes, array $cardFilters = []): array
    {
//        $filteredReadingTypes = [];
//        foreach ($allReadingTypes as $readingType) {
//            if (!$readingType instanceof AllSensorReadingTypeInterface) {
//               continue;
//            }
//            if (in_array($readingType->getSensorTypeName(), $cardFilters)) {
//                $filteredReadingTypes[$readingType->getReadingType()] = $readingType;
//            }
//            $filteredReadingTypes[] = new CardFilteredReadingTypeDTO(
//                $readingType->getSensorReadingTypeObjectString(),
//                $readingType->getSensorTypeName(),
//            );
//        }
//
//        return $filteredReadingTypes;
//        return array_filter($allReadingTypes, static function ($readingTypes) use ($cardFilters) {
//            return (!in_array($readingTypes, $cardFilters, true))
//                ?  $readingTypes
//                : false;
//        });
        return [];
    }



//        return $filteredSensorTypes;
//        return array_filter($sensorTypes, static function ($sensorType) use ($cardFilters) {
//            /** @var SensorType $sensorType */
//            return (!in_array($sensorType->getSensorTypeID(), $cardFilters, true))
//                ?  $sensorType
//                : false;
//        });
//    }
}
