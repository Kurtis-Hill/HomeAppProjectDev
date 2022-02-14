<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator;

use App\Common\Traits\ValidatorProcessorTrait;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingTypeRepositoryFactoryInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SensorReadingTypesValidatorService implements SensorReadingTypesValidatorServiceInterface
{
    use ValidatorProcessorTrait;

    private SensorReadingTypeRepositoryFactoryInterface $sensorReadingTypeFactory;

    private ValidatorInterface $validator;

    public function __construct(SensorReadingTypeRepositoryFactoryInterface $readingTypeFactory, ValidatorInterface $validator,)
    {
        $this->sensorReadingTypeFactory = $readingTypeFactory;
        $this->validator = $validator;
    }

    #[ArrayShape(["errors"])]
    public function validateSensorReadingTypeObjectsBySensorTypeObject(SensorTypeInterface $sensorTypeObject): array
    {
        $sensorType = $sensorTypeObject->getSensorTypeName();

        $errors = [];
        if ($sensorTypeObject instanceof TemperatureSensorTypeInterface) {
            $validationErrors = $this->performSensorReadingTypeObjectValidatorAndSave(
                $sensorTypeObject->getTempObject(),
                $sensorType
            );

            if (!empty($validationErrors)) {
                $errors = array_merge($errors, $validationErrors);
            }
        }

        if ($sensorTypeObject instanceof HumiditySensorTypeInterface) {
            $validationErrors = $this->performSensorReadingTypeObjectValidatorAndSave(
                $sensorTypeObject->getHumidObject(),
                $sensorType
            );

            if (!empty($validationErrors)) {
                $errors = array_merge($errors, $validationErrors);
            }
        }
        if ($sensorTypeObject instanceof LatitudeSensorTypeInterface) {
            $validationErrors = $this->performSensorReadingTypeObjectValidatorAndSave(
                $sensorTypeObject->getLatitudeObject(),
                $sensorType
            );

            if (!empty($validationErrors)) {
                $errors = array_merge($errors, $validationErrors);
            }
        }
        if ($sensorTypeObject instanceof AnalogSensorTypeInterface) {
            $validationErrors = $this->performSensorReadingTypeObjectValidatorAndSave(
                $sensorTypeObject->getAnalogObject(),
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

    #[ArrayShape(['string'])]
    public function validateSensorReadingTypeObject(
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
        string $sensorType
    ): array
    {
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

    private function performSensorReadingTypeValidation(
        AllSensorReadingTypeInterface $sensorReadingType,
        ?string $sensorTypeName = null
    ): array {
        if ($sensorReadingType instanceof Humidity || $sensorReadingType instanceof Latitude) {
            $validationErrors = $this->validator->validate(
                $sensorReadingType,
            );
        } else {
            $validationErrors = $this->validator->validate(
                $sensorReadingType,
                null,
                $sensorTypeName
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
