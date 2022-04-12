<?php

namespace App\Sensors\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\API\APIErrorMessages;
use App\Common\Traits\ValidatorProcessorTrait;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBuilderInterface;
use App\Sensors\DTO\Request\CurrentReadingRequest\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateDTO;
use App\Sensors\Exceptions\SensorReadingUpdateFactoryException;
use App\Sensors\Factories\ORMFactories\SensorReadingType\SensorReadingUpdateFactory;
use App\Sensors\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CurrentReadingSensorDataRequestHandler implements CurrentReadingSensorDataRequestHandlerInterface
{
    use ValidatorProcessorTrait;

    private ValidatorInterface $validator;

    private SensorReadingUpdateFactory $sensorReadingUpdateFactory;

    private array $allSensorTypes;

    private array $validationErrors = [];

    private array $errors = [];

    public function __construct(
        ValidatorInterface $validator,
        SensorTypeRepositoryInterface $sensorTypeRepository,
        SensorReadingUpdateFactory $sensorReadingUpdateFactory,
    ) {
        $this->validator = $validator;
        $this->sensorReadingUpdateFactory = $sensorReadingUpdateFactory;
        try {
            $this->allSensorTypes = $sensorTypeRepository->getAllSensorTypeNames();
        } catch (ORMException) {
            $this->errors[] = sprintf(APIErrorMessages::QUERY_FAILURE, 'Sensor type');
        }
    }

    public function validateSensorDataRequest(SensorDataCurrentReadingUpdateDTO $sensorDataCurrentReadingUpdateDTO): bool
    {
        $passedValidation = true;
        $objectValidationErrors = $this->validator->validate($sensorDataCurrentReadingUpdateDTO);
        if ($this->checkIfErrorsArePresent($objectValidationErrors)) {
            foreach ($objectValidationErrors as $error) {
                $this->validationErrors[] = $this->getValidationErrorsAsStrings($error)->current();
            }
            $passedValidation = false;
        }
        if (!in_array($sensorDataCurrentReadingUpdateDTO->getSensorType(), $this->allSensorTypes, true)) {
            $this->errors[] = sprintf(APIErrorMessages::OBJECT_NOT_RECOGNISED, 'Sensor type ' . $sensorDataCurrentReadingUpdateDTO->getSensorType());
            $passedValidation = false;
        }

        return $passedValidation;
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

    public function validateSensorTypeDTO(
        AbstractCurrentReadingUpdateRequestDTO $currentReadingUpdateRequestDTO,
        string $sensorType
    ): bool {
        $objectValidationErrors = $this->validator->validate($currentReadingUpdateRequestDTO, null, $sensorType);
        if ($this->checkIfErrorsArePresent($objectValidationErrors)) {
            foreach ($objectValidationErrors as $error) {
                $this->validationErrors[] = $this->getValidationErrorsAsStrings($error)->current();
            }
            return false;
        }

        return true;
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
