<?php

namespace App\Sensors\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Common\API\APIErrorMessages;
use App\Common\Traits\ValidatorProcessorTrait;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBuilderInterface;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateDTO;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Exceptions\SensorReadingUpdateFactoryException;
use App\Sensors\Exceptions\SensorTypeNotFoundException;
use App\Sensors\Factories\ORMFactories\SensorReadingType\SensorReadingUpdateFactory;
use App\Sensors\Factories\SensorTypeReadingTypeCheckerFactory\SensorTypeReadingTypeCheckerFactory;
use App\Sensors\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CurrentReadingSensorDataRequestHandler implements CurrentReadingSensorDataRequestHandlerInterface
{
    public const SENSOR_UPDATE_SUCCESS_MESSAGE = '%s data accepted for sensor %s';

    use ValidatorProcessorTrait;

    private ValidatorInterface $validator;

    private SensorReadingUpdateFactory $sensorReadingUpdateFactory;

    private SensorTypeReadingTypeCheckerFactory $sensorTypeReadingTypeCheckerFactory;

    private array $allSensorTypes = [];

    private int $readingTypeRequestAttempt = 0;

    private array $successfulRequests = [];

    private array $validationErrors = [];

    private array $errors = [];

    public function __construct(
        ValidatorInterface $validator,
        SensorTypeRepositoryInterface $sensorTypeRepository,
        SensorReadingUpdateFactory $sensorReadingUpdateFactory,
        SensorTypeReadingTypeCheckerFactory $sensorTypeReadingTypeCheckerFactory,
    ) {
        $this->validator = $validator;
        $this->sensorReadingUpdateFactory = $sensorReadingUpdateFactory;
        $this->sensorTypeReadingTypeCheckerFactory = $sensorTypeReadingTypeCheckerFactory;
        try {
            $this->allSensorTypes = $sensorTypeRepository->getAllSensorTypeNames();
        } catch (ORMException) {
            $this->errors[] = sprintf(APIErrorMessages::QUERY_FAILURE, 'Sensor type');
        }
    }

    public function handleSensorUpdateRequest(SensorDataCurrentReadingUpdateDTO $sensorDataCurrentReadingUpdateDTO): bool
    {
        return $this->validateSensorDataRequest($sensorDataCurrentReadingUpdateDTO);
    }

    private function validateSensorDataRequest(SensorDataCurrentReadingUpdateDTO $sensorDataCurrentReadingUpdateDTO): bool
    {
        $passedValidation = true;
        $objectValidationErrors = $this->validator->validate($sensorDataCurrentReadingUpdateDTO);
        if ($this->checkIfErrorsArePresent($objectValidationErrors)) {
            foreach ($objectValidationErrors as $error) {
                $this->validationErrors[] = $this->getValidationErrorsAsStrings($error);
            }
            $passedValidation = false;
        }
        if (!in_array($sensorDataCurrentReadingUpdateDTO->getSensorType(), $this->allSensorTypes, true)) {
            $this->errors[] = sprintf(APIErrorMessages::OBJECT_NOT_RECOGNISED, 'Sensor type ' . $sensorDataCurrentReadingUpdateDTO->getSensorType());
            $passedValidation = false;
        }

        return $passedValidation;
    }

    #[ArrayShape(
        [
            AnalogCurrentReadingUpdateRequestDTO::class,
            HumidityCurrentReadingUpdateRequestDTO::class,
            LatitudeCurrentReadingUpdateRequestDTO::class,
            TemperatureCurrentReadingUpdateRequestDTO::class,
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

            $sensorTypeUpdateDTOBuilder = $this->getSensorTypeUpdateDTOBuilder($readingType);
            if ($sensorTypeUpdateDTOBuilder === null) {
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
            $readingTypeCurrentReadingDTOs[] = $sensorTypeUpdateDTOBuilder->buildRequestCurrentReadingUpdateDTO($currentReading);
            $this->successfulRequests[] = sprintf(self::SENSOR_UPDATE_SUCCESS_MESSAGE, $readingTypeCurrentReadingDTO->getReadingType(), $sensorDataCurrentReadingUpdateDTO->getSensorName());
        }

        return $readingTypeCurrentReadingDTOs ?? [];
    }

    private function checkSensorReadingTypeIsAllowed(string $readingType, string $sensorType): bool
    {
        try {
            $sensorReadingTypeChecker = $this->sensorTypeReadingTypeCheckerFactory->fetchSensorReadingTypeChecker($sensorType);
        } catch (SensorTypeNotFoundException $e) {
            $this->errors[] = $e->getMessage();

            return false;
        }

        $readingTypeValidForSensorType = $sensorReadingTypeChecker->checkReadingTypeIsValid($readingType);

        if ($readingTypeValidForSensorType === false) {
            $this->errors[] = sprintf(APIErrorMessages::READING_TYPE_NOT_VALID_FOR_SENSOR, $readingType, $sensorType);

            return false;
        }

        return true;
    }

    public function getSensorTypeUpdateDTOBuilder(string $readingType): ?ReadingTypeUpdateBuilderInterface
    {
        try {
            return $this->sensorReadingUpdateFactory->getReadingTypeUpdateBuilder($readingType);
        } catch (SensorReadingUpdateFactoryException $e) {
            $this->errors[] = $e->getMessage();
        }

        return null;
    }

    /**
     * @throws ReadingTypeNotSupportedException
     */

    private function validateSensorTypeDTO(
        AbstractCurrentReadingUpdateRequestDTO $currentReadingUpdateRequestDTO,
        string $sensorType
    ): bool {
        $objectValidationErrors = $this->validator->validate($currentReadingUpdateRequestDTO, null, $sensorType);
        if ($this->checkIfErrorsArePresent($objectValidationErrors)) {
            foreach ($objectValidationErrors as $error) {
                $this->validationErrors[] = $this->getValidationErrorsAsStrings($error);
            }
            return false;
        }

        return true;
    }

    #[ArrayShape(['temperature data accepted for sensor <sensor-name>'])]
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

    #[ArrayShape(['errors'])]
    public function getErrors(): array
    {
        return $this->errors;
    }
}
