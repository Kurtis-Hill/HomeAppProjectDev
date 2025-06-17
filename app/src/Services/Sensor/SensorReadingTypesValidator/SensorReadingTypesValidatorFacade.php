<?php

namespace App\Services\Sensor\SensorReadingTypesValidator;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\LatitudeReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Factories\Sensor\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Traits\ValidatorProcessorTrait;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;

//@When New Sensor Reading Type
class SensorReadingTypesValidatorFacade implements SensorReadingTypesValidatorInterface
{
    use ValidatorProcessorTrait;

    private SensorReadingTypeRepositoryFactory $sensorReadingTypeFactory;

    private ValidatorInterface $validator;

    public function __construct(
        SensorReadingTypeRepositoryFactory $readingTypeFactory,
        ValidatorInterface $validator,
    ) {
        $this->sensorReadingTypeFactory = $readingTypeFactory;
        $this->validator = $validator;
    }

    #[ArrayShape(["errors"])]
    public function validateSensorReadingTypeObjectsBySensorTypeObject(
        SensorTypeInterface $sensorTypeObject
    ): array {
        $sensorType = $sensorTypeObject->getSensorTypeName();

        $errors = [];
        if ($sensorTypeObject instanceof TemperatureReadingTypeInterface) {
            $validationErrors = $this->performSensorReadingTypeObjectValidatorAndSave(
                $sensorTypeObject->getTemperature(),
                $sensorType
            );

            if (!empty($validationErrors)) {
                $errors = $validationErrors;
            }
        }

        if ($sensorTypeObject instanceof HumidityReadingTypeInterface) {
            $validationErrors = $this->performSensorReadingTypeObjectValidatorAndSave(
                $sensorTypeObject->getHumidObject(),
                $sensorType
            );

            if (!empty($validationErrors)) {
                $errors = array_merge($errors, $validationErrors);
            }
        }
        if ($sensorTypeObject instanceof LatitudeReadingTypeInterface) {
            $validationErrors = $this->performSensorReadingTypeObjectValidatorAndSave(
                $sensorTypeObject->getLatitudeObject(),
                $sensorType
            );

            if (!empty($validationErrors)) {
                $errors = array_merge($errors, $validationErrors);
            }
        }
        if ($sensorTypeObject instanceof AnalogReadingTypeInterface) {
            $validationErrors = $this->performSensorReadingTypeObjectValidatorAndSave(
                $sensorTypeObject->getAnalogObject(),
                $sensorType
            );

            if (!empty($validationErrors)) {
                $errors = array_merge($errors, $validationErrors);
            }
        }
        if ($sensorTypeObject instanceof RelayReadingTypeInterface) {
            $validationErrors = $this->performSensorReadingTypeObjectValidatorAndSave(
                $sensorTypeObject->getRelay(),
                $sensorType
            );

            if (!empty($validationErrors)) {
                $errors = array_merge($errors, $validationErrors);
            }
        }
        if ($sensorTypeObject instanceof MotionSensorReadingTypeInterface) {
            $validationErrors = $this->performSensorReadingTypeObjectValidatorAndSave(
                $sensorTypeObject->getMotion(),
                $sensorType
            );

            if (!empty($validationErrors)) {
                $errors = array_merge($errors, $validationErrors);
            }
        }

        return $errors;
    }

    private function performSensorReadingTypeObjectValidatorAndSave(
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
        string $sensorType,
    ): array {
        $sensorTypeObjectErrors = $this->performSensorReadingTypeValidation(
            $sensorReadingTypeObject,
            $sensorType
        );
        if (!empty($sensorTypeObjectErrors)) {
            $errors = $sensorTypeObjectErrors;
        } else {
            try {
                $this->saveReadingType($sensorReadingTypeObject);
            } catch (SensorReadingTypeRepositoryFactoryException $e) {
                $errors = [$e->getMessage()];
            }
        }

        return $errors ?? [];
    }

    #[ArrayShape(['validationErrors'])]
    public function validateSensorReadingTypeObject(
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
        string $sensorType
    ): array {
        $validationErrors = $this->performSensorReadingTypeValidation(
            $sensorReadingTypeObject,
            $sensorType
        );
        if (empty($validationErrors)) {
            try {
                $this->saveReadingType($sensorReadingTypeObject);
            } catch (SensorReadingTypeRepositoryFactoryException $e) {
                return [$e->getMessage()];
            }
        }

        return $validationErrors;
    }

    #[ArrayShape(['validationErrors'])]
    private function performSensorReadingTypeValidation(
        AllSensorReadingTypeInterface $sensorReadingType,
        ?string $sensorTypeName = null
    ): array {
        if ($sensorReadingType instanceof Temperature || $sensorReadingType instanceof Analog) {
            $validationErrors = $this->validator->validate(
                $sensorReadingType,
                null,
                $sensorTypeName
            );
        } else {
            $validationErrors = $this->validator->validate(
                $sensorReadingType,
            );
        }

        return $this->checkIfErrorsArePresent($validationErrors)
            ? $this->getValidationErrorAsArray($validationErrors)
            : [];
    }

    /**
     * @throws SensorReadingTypeRepositoryFactoryException
     */
    private function saveReadingType(AllSensorReadingTypeInterface $readingTyeObject): void
    {
        $repository = $this->sensorReadingTypeFactory->getSensorReadingTypeRepository(
            $readingTyeObject->getReadingType()
        );
        $repository->persist($readingTyeObject);
    }
}
