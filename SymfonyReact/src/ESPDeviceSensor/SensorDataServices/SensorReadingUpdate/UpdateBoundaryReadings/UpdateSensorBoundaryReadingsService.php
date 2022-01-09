<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Factories\ReadingTypeQueryBuilderFactory\ReadingTypeQueryFactory;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator\SensorReadingTypesValidatorServiceInterface;
use App\ESPDeviceSensor\Builders\ReadingTypeQueryDTOBuilders\ReadingTypeQueryDTOBuilderInterface;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\ESPDeviceSensor\Factories\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use http\Exception\UnexpectedValueException;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class UpdateSensorBoundaryReadingsService implements UpdateSensorBoundaryReadingsServiceInterface
{
    private ReadingTypeQueryFactory $readingTypeQueryFactory;

    private SensorRepositoryInterface $sensorRepository;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;#

    private SensorReadingTypesValidatorServiceInterface $sensorReadingTypesValidatorService;

    public function __construct(
        ReadingTypeQueryFactory $readingTypeQuery,
        SensorTypeQueryFactory $sensorTypeQuery,
        SensorRepositoryInterface $sensorRepository,
        SensorReadingTypesValidatorServiceInterface $sensorReadingTypesValidatorService,
    )
    {
        $this->readingTypeQueryFactory = $readingTypeQuery;
        $this->sensorRepository = $sensorRepository;
        $this->sensorTypeQueryFactory = $sensorTypeQuery;
        $this->sensorReadingTypesValidatorService = $sensorReadingTypesValidatorService;
    }

    private function getReadingTypeQueryDTOBuilder(UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): ReadingTypeQueryDTOBuilderInterface
    {
        return $this->readingTypeQueryFactory->getReadingTypeQueryDTOBuilder($updateSensorBoundaryReadingsDTO->getSensorType());
    }

    private function updateSensorBoundaryReading(
        StandardReadingSensorInterface $standardReadingSensor,
        UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO
    ): void {
        $standardReadingSensor->setHighReading($updateSensorBoundaryReadingsDTO->getHighReading());
        $standardReadingSensor->setLowReading($updateSensorBoundaryReadingsDTO->getLowReading());
        $standardReadingSensor->setConstRecord($updateSensorBoundaryReadingsDTO->getConstRecord());
    }

    public function getReadingTypeObjectJoinQueryDTO(string $sensorName): JoinQueryDTO
    {
        return $this->sensorTypeQueryFactory->getSensorTypeQueryDTOBuilder($sensorName)->buildSensorTypeQueryJoinDTO();
    }

    #[ArrayShape([Temperature::class, Humidity::class])]
    public function findSensorAndReadingTypesToUpdateBoundaryReadings(JoinQueryDTO $readingTypeJoinQueryDTO, array $readingTypeObjectsJoinDTOs, int $deviceID, string $sensorName): array
    {
        $sensorTypeObjectArray = $this->sensorRepository->getSensorTypeAndReadingTypeObjectsForSensor(
            $readingTypeJoinQueryDTO,
            $deviceID,
            $readingTypeObjectsJoinDTOs,
            $sensorName,
        );

        return $sensorTypeObjectArray;
    }

    #[Pure]
    public function createUpdateSensorBoundaryReadingDTO(array $updateData): UpdateSensorBoundaryReadingsDTO
    {
        return new UpdateSensorBoundaryReadingsDTO(
            $updateData['sensorType'],
            $updateData['highReading'],
            $updateData['lowReading'],
            $updateData['constRecord'],
        );
    }

    public function processBoundaryReadingDTOs(array $updateSensorBoundaryReadingsDTOs, array $readingTypeObjects, string $sensorTypeName): array
    {
        $validationErrors = [];

        foreach ($updateSensorBoundaryReadingsDTOs as $updateSensorBoundaryReadingsDTO) {
            try {
                if (!$updateSensorBoundaryReadingsDTO instanceof UpdateSensorBoundaryReadingsDTO) {
                    throw new UnexpectedValueException('You have not passed the correct DTO for this service to process request');
                }
                foreach ($readingTypeObjects as $sensorReadingTypeObject) {
                    if (!$sensorReadingTypeObject instanceof StandardReadingSensorInterface || !$sensorReadingTypeObject instanceof AllSensorReadingTypeInterface) {
                        throw new UnexpectedValueException('You have not passed the correct sensor reading type for this service to process request');
                    }
                    if ($sensorReadingTypeObject->getSensorTypeName() === $updateSensorBoundaryReadingsDTO->getSensorType()) {
                        $this->updateSensorBoundaryReading(
                            $sensorReadingTypeObject,
                            $updateSensorBoundaryReadingsDTO
                        );
                        $validationError = $this->sensorReadingTypesValidatorService->validateSensorReadingTypeObject(
                            $sensorReadingTypeObject,
                            $sensorTypeName
                        );
                        if (!empty($validationError)) {
                            foreach ($validationError as $error) {
                                $validationErrors[] = $error;
                            }
                        }
                    }
                }
            } catch (UnexpectedValueException $e) {
                $validationErrors[] = $e->getMessage();
            }

        }

        return $validationErrors ?? [];
    }

    public function createReadingTypeQueryDTO(UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): JoinQueryDTO
    {
        $sensorTypeQueryDTOBuilder = $this->getReadingTypeQueryDTOBuilder($updateSensorBoundaryReadingsDTO);

        return $sensorTypeQueryDTOBuilder->buildReadingTypeJoinQueryDTO();
    }
}
