<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading;

use App\Common\API\APIErrorMessages;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\Builders\CurrentReadingDTOBuilders\CurrentReadingUpdateDTOBuilder;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\CurrentReadingUpdateRequestBuilderInterface;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBoundaryReadingBuilderInterface;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBuilderInterface;
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
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Exceptions\SensorReadingUpdateFactoryException;
use App\Sensors\Exceptions\SensorTypeNotFoundException;
use App\Sensors\Factories\SensorReadingType\SensorReadingUpdateFactory;
use App\Sensors\Factories\SensorTypeReadingTypeCheckerFactory\SensorTypeCheckerFactory;
use App\Sensors\Repository\Sensors\SensorTypeRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CurrentReadingSensorDataRequestHandler implements CurrentReadingSensorDataRequestHandlerInterface
{
    public const SENSOR_UPDATE_SUCCESS_MESSAGE = '%s data accepted for sensor %s';

    use ValidatorProcessorTrait;

    private ValidatorInterface $validator;

    private SensorReadingUpdateFactory $sensorReadingUpdateFactory;

    private SensorTypeCheckerFactory $sensorTypeTypeCheckerFactory;

    #[ArrayShape([Bmp::NAME, Dallas::NAME, Dht::NAME, Soil::NAME, GenericMotion::NAME, GenericRelay::NAME])]
    private array $allSensorTypes = [];

    private int $readingTypeRequestAttempt = 0;

    #[ArrayShape([CurrentReadingUpdateResponseDTO::class])]
    private array $successfulRequests = [];

    #[ArrayShape([CurrentReadingUpdateResponseDTO::class])]
    private array $validationErrors = [];

    #[ArrayShape([CurrentReadingUpdateResponseDTO::class])]
    private array $errors = [];

    public function __construct(
        ValidatorInterface $validator,
        SensorReadingUpdateFactory $sensorReadingUpdateFactory,
        SensorTypeCheckerFactory $sensorTypeReadingTypeCheckerFactory,
    ) {
        $this->validator = $validator;
        $this->sensorReadingUpdateFactory = $sensorReadingUpdateFactory;
        $this->sensorTypeTypeCheckerFactory = $sensorTypeReadingTypeCheckerFactory;
    }

    public function processSensorUpdateData(SensorDataCurrentReadingUpdateDTO $sensorDataCurrentReadingUpdateDTO, array $validationGroups): bool
    {
        return $this->validateSensorData($sensorDataCurrentReadingUpdateDTO, $validationGroups);
    }

    private function validateSensorData(SensorDataCurrentReadingUpdateDTO $sensorDataCurrentReadingUpdateDTO, array $validationGroups): bool
    {
//        dd($validationGroups);
        $objectValidationErrors = $this->validator->validate(
            $sensorDataCurrentReadingUpdateDTO,
            null,
            $validationGroups
        );
//        dd($objectValidationErrors, $validationGroups);
        if ($this->checkIfErrorsArePresent($objectValidationErrors)) {
            foreach ($objectValidationErrors as $error) {
                $this->validationErrors[] = CurrentReadingUpdateDTOBuilder::buildCurrentReadingErrorResponseDTO($this->getValidationErrorsAsStrings($error));
//                $validationErrors[] = CurrentReadingUpdateDTOBuilder::buildCurrentReadingErrorResponseDTO($this->getValidationErrorsAsStrings($error));
            }
            $passedValidation = false;
        }
        //@TODO DELETE THIS
//        if (!in_array($sensorDataCurrentReadingUpdateDTO->getSensorType(), $this->allSensorTypes, true)) {
//            $this->errors[] = CurrentReadingUpdateDTOBuilder::buildCurrentReadingErrorResponseDTO(
//                sprintf(
//                    APIErrorMessages::OBJECT_NOT_RECOGNISED,
//                    'Sensor type ' . $sensorDataCurrentReadingUpdateDTO->getSensorType()
//                )
//            );
//            $passedValidation = false;
//        }

        return $passedValidation ?? true;
//        return $validationErrors ?? [];
    }

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

            $sensorTypeUpdateDTOBuilder = $this->getSensorTypeUpdateDTOBuilder($readingType);
            if (!$sensorTypeUpdateDTOBuilder instanceof CurrentReadingUpdateRequestBuilderInterface) {
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
            $this->successfulRequests[] = CurrentReadingUpdateDTOBuilder::buildCurrentReadingSuccessUpdateDTO(
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
            $this->errors[] = CurrentReadingUpdateDTOBuilder::buildCurrentReadingErrorResponseDTO($e->getMessage());

            return false;
        }

        $readingTypeValidForSensorType = $sensorReadingTypeChecker->checkReadingTypeIsValid($readingType);

        if ($readingTypeValidForSensorType === false) {
            $this->errors[] = CurrentReadingUpdateDTOBuilder::buildCurrentReadingErrorResponseDTO(
                sprintf(
                    APIErrorMessages::READING_TYPE_NOT_VALID_FOR_SENSOR,
                    $readingType,
                    $sensorType
                )
            );

            return false;
        }

        return true;
    }

    public function getSensorTypeUpdateDTOBuilder(string $readingType): ?ReadingTypeUpdateBoundaryReadingBuilderInterface
    {
        try {
            return $this->sensorReadingUpdateFactory->getReadingTypeUpdateBuilder($readingType);
        } catch (SensorReadingUpdateFactoryException $e) {
            $this->errors[] = CurrentReadingUpdateDTOBuilder::buildCurrentReadingErrorResponseDTO($e->getMessage());
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
                $this->validationErrors[] = CurrentReadingUpdateDTOBuilder::buildCurrentReadingErrorResponseDTO($this->getValidationErrorsAsStrings($error));
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

    #[ArrayShape(['errors'])]
    public function getErrors(): array
    {
        return $this->errors;
    }
}
