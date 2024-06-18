<?php

namespace App\Services\Sensor\SensorReadingUpdate\CurrentReading;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\CurrentReadingUpdateRequestBuilderInterface;
use App\Builders\Sensor\Response\MessageResponseBuilders\CurrentReadingMessageUpdateDTOBuilder;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Response\CurrentReadingResponse\CurrentReadingUpdateResponseDTO;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Exceptions\Sensor\SensorReadingUpdateFactoryException;
use App\Exceptions\Sensor\SensorTypeNotFoundException;
use App\Factories\Sensor\SensorReadingType\SensorReadingUpdateFactory;
use App\Factories\Sensor\SensorTypeReadingTypeCheckerFactory\SensorTypeCheckerFactory;
use App\Services\API\APIErrorMessages;
use App\Traits\ValidatorProcessorTrait;
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
    public function handleCurrentReadingDTOCreation(SensorDataCurrentReadingUpdateRequestDTO $sensorDataCurrentReadingUpdateDTO): array
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
