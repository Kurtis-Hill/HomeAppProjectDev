<?php

namespace App\UserInterface\Services\Cards\CardDataFilterService;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\SensorTypeBuilderFailureException;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use App\UserInterface\DTO\CardDataFiltersDTO\CardDataPostFilterDTO;
use App\UserInterface\DTO\CardDataFiltersDTO\CardDataPreFilterDTO;
use App\UserInterface\DTO\CardDataFiltersDTO\CardFilteredReadingTypeDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeQueryDTO;
use App\UserInterface\Factories\CardQueryBuilderFactories\SensorTypeQueryFactory;
use Doctrine\ORM\ORMException;
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

    public function filterSensorTypes(CardDataPreFilterDTO $cardFilters): CardDataPostFilterDTO
    {
        $allSensorTypes = $this->sensorTypeRepository->findAll();

        $sensorTypesToQuery = $this->filterSensorByType($allSensorTypes, $cardFilters->getSensorTypesToFilter());

        $allReadingTypes = SensorType::SENSOR_READING_TYPE_DATA;

        $readingTypesToQuery = $this->filterSensorByReadingType(array_keys($allReadingTypes), $cardFilters->getReadingTypesToFilter());

        return new CardDataPostFilterDTO(
            $sensorTypesToQuery,
            $readingTypesToQuery
        );
    }

    #[ArrayShape([CardSensorTypeQueryDTO::class])]
    private function filterSensorByType(array $sensorTypes, array $sensorTypeIdsToFilter = []): array
    {
        foreach ($sensorTypes as $sensorType) {
            if (!$sensorType instanceof SensorTypeInterface) {
                continue;
            }
            if (!in_array($sensorType->getSensorTypeID(), $sensorTypeIdsToFilter, true)) {
                try {
                    $queryTypeBuilder = $this->sensorTypeQueryFactory->getSensorTypeQueryDTOBuilder($sensorType->getSensorTypeName());
                    $sensorTypesToQuery[] = $queryTypeBuilder->buildSensorTypeQueryDTO();
                } catch (SensorTypeBuilderFailureException) {
                    error_log('failed to retrieve query dto builder for' . $sensorType->getSensorTypeName());
                }
            }
        }

        return $sensorTypesToQuery ?? [];
    }


        // make new table for different sensor types
    private function filterSensorByReadingType(array $allReadingTypes, array $cardFilters = []): array
    {
        $filteredReadingTypes = [];
        foreach ($allReadingTypes as $readingType) {
            if (!$readingType instanceof AllSensorReadingTypeInterface) {
               continue;
            }
            if (in_array($readingType->getSensorTypeName(), $cardFilters)) {
                $filteredReadingTypes[$readingType->getReadingType()] = $readingType;
            }
            $filteredReadingTypes[] = new CardFilteredReadingTypeDTO(
                $readingType->getSensorReadingTypeObjectString(),
                $readingType->getSensorTypeName(),
            );
        }

        return $filteredReadingTypes;
//        return array_filter($allReadingTypes, static function ($readingTypes) use ($cardFilters) {
//            return (!in_array($readingTypes, $cardFilters, true))
//                ?  $readingTypes
//                : false;
//        });
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
