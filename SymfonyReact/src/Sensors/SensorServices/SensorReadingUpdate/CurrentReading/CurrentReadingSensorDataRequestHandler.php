<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading;

use App\Common\API\APIErrorMessages;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\Builders\CurrentReadingDTOBuilders\CurrentReadingMessageUpdateDTOBuilder;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\CurrentReadingUpdateRequestBuilderInterface;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateDTO;
use App\Sensors\DTO\Response\CurrentReadingResponse\CurrentReadingUpdateResponseDTO;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\SensorReadingUpdateFactoryException;
use App\Sensors\Exceptions\SensorTypeNotFoundException;
use App\Sensors\Factories\SensorReadingType\SensorReadingUpdateFactory;
use App\Sensors\Factories\SensorTypeReadingTypeCheckerFactory\SensorTypeCheckerFactory;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CurrentReadingSensorDataRequestHandler implements CurrentReadingSensorDataRequestHandlerInterface
{
    public const SENSOR_UPDATE_SUCCESS_MESSAGE = '%s data accepted for sensor %s';

    use ValidatorProcessorTrait;

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SensorReadingUpdateFactory $sensorReadingUpdateFactory,
        private readonly SensorTypeCheckerFactory $sensorTypeReadingTypeCheckerFactory,
        private readonly  SensorTypeCheckerFactory $sensorTypeTypeCheckerFactory,
        #[ArrayShape([Bmp::NAME, Dallas::NAME, Dht::NAME, Soil::NAME, GenericMotion::NAME, GenericRelay::NAME])]
        private readonly array $allSensorTypes = [],
        private int $readingTypeRequestAttempt = 0,
        #[ArrayShape([CurrentReadingUpdateResponseDTO::class])]
        private array $successfulRequests = [],
        #[ArrayShape([CurrentReadingUpdateResponseDTO::class])]
        private array $validationErrors = [],
    ) {}

    #[ArrayShape(
        [
            AnalogCurrentReadingUpdateRequestDTO::class,
            HumidityCurrentReadingUpdateRequestDTO::class,
            LatitudeCurrentReadingUpdateRequestDTO::class,
            TemperatureCurrentReadingUpdateRequestDTO::class,
            BoolCurrentReadingUpdateRequestDTO::class,
        ]
    )]
    public function handleCurrentReadingDTOCreation(SensorDataCurrentReadingUpdateDTO $sensorDataCurrentReadingUpdateDTO): array
    {
        foreach ($sensorDataCurrentReadingUpdateDTO->getCurrentReadings() as $readingType => $currentReading) {
            ++$this->readingTypeRequestAttempt;

            $readingTypeValidForSensorType = $this->checkSensorReadingTypeIsAllowed(
                $readingType,
                $sensorDataCurrentReadingUpdateDTO->getSensorType()
            );

            if ($readingTypeValidForSensorType === false) {
                continue;
            }

            try {
                $sensorTypeUpdateDTOBuilder = $this->sensorReadingUpdateFactory->getReadingTypeUpdateBuilder($readingType);
            } catch (SensorReadingUpdateFactoryException $e) {
                $this->validationErrors[] = $e->getMessage();
                continue;
            }
            if (!$sensorTypeUpdateDTOBuilder instanceof CurrentReadingUpdateRequestBuilderInterface) {
                $this->validationErrors[] = 'Sensor type update builder is not an instance of CurrentReadingUpdateRequestBuilderInterface';
                continue;
            }
            $readingTypeCurrentReadingDTO = $sensorTypeUpdateDTOBuilder->buildRequestCurrentReadingUpdateDTO($currentReading);
            $sensorTypeReadingValidationPassed = $this->validateSensorTypeDTO(
                $readingTypeCurrentReadingDTO,
                $sensorDataCurrentReadingUpdateDTO->getSensorType()
            );

            if ($sensorTypeReadingValidationPassed === false) {
                continue;
            }
            $readingTypeCurrentReadingDTOs[] = $readingTypeCurrentReadingDTO;
            $this->successfulRequests[] = CurrentReadingMessageUpdateDTOBuilder::buildCurrentReadingSuccessUpdateDTO(
                $readingTypeCurrentReadingDTO->getReadingType(),
                $sensorDataCurrentReadingUpdateDTO->getSensorName()
            );
        }

        return $readingTypeCurrentReadingDTOs ?? [];
    }

    private function checkSensorReadingTypeIsAllowed(string $readingType, string $sensorType): bool
    {
        try {
            $sensorReadingTypeChecker = $this->sensorTypeTypeCheckerFactory->fetchSensorReadingTypeChecker($sensorType);
        } catch (SensorTypeNotFoundException $e) {
            $this->validationErrors = [
                $e->getMessage(),
                ...$this->validationErrors
            ];

            return false;
        }

        $readingTypeValidForSensorType = $sensorReadingTypeChecker->checkReadingTypeIsValid($readingType);

        if ($readingTypeValidForSensorType === false) {
            $this->validationErrors = [
                sprintf(
                    APIErrorMessages::READING_TYPE_NOT_VALID_FOR_SENSOR,
                    $readingType,
                    $sensorType
                ),
                ...$this->validationErrors,
            ];

            return false;
        }

        return true;
    }

    private function validateSensorTypeDTO(
        AbstractCurrentReadingUpdateRequestDTO $currentReadingUpdateRequestDTO,
        string $sensorType
    ): bool {
        $objectValidationErrors = $this->validator->validate(value: $currentReadingUpdateRequestDTO, groups: $sensorType);
        if ($this->checkIfErrorsArePresent($objectValidationErrors)) {
            foreach ($objectValidationErrors as $error) {
                $this->validationErrors = [
                    $this->getValidationErrorsAsStrings($error),
                    ...$this->validationErrors
                ];
            }
            return false;
        }

        return true;
    }

    #[ArrayShape([CurrentReadingUpdateResponseDTO::class])]
    public function getSuccessfulRequests(): array
    {
        return $this->successfulRequests;
    }

    public function getReadingTypeRequestAttempt(): int
    {
        return $this->readingTypeRequestAttempt;
    }


    #[ArrayShape(['validationErrors'])]
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}
