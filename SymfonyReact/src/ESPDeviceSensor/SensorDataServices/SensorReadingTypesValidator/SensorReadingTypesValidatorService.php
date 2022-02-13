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
            $sensorTypeObjectErrors = $this->performSensorReadingTypeValidation(
                $sensorTypeObject->getTempObject(),
                $sensorType
            );
            if (!empty($sensorTypeObjectErrors)) {
                $errors = [$sensorTypeObjectErrors];
            } else {
                $this->saveReadingType($sensorTypeObject->getTempObject());
            }
        }
        if ($sensorTypeObject instanceof HumiditySensorTypeInterface) {
            $sensorTypeObjectErrors = $this->performSensorReadingTypeValidation(
                $sensorTypeObject->getHumidObject(),
                $sensorType
            );
            if (!empty($sensorTypeObjectErrors)) {
                $errors = [...$errors, $sensorTypeObjectErrors];
            } else {
                $this->saveReadingType($sensorTypeObject->getHumidObject());
            }
        }
        if ($sensorTypeObject instanceof LatitudeSensorTypeInterface) {
            $sensorTypeObjectErrors = $this->performSensorReadingTypeValidation(
                $sensorTypeObject->getLatitudeObject(),
                $sensorType
            );
            if (!empty($sensorTypeObjectErrors)) {
                $errors = [...$errors, $sensorTypeObjectErrors];
            } else {
                $this->saveReadingType($sensorTypeObject->getLatitudeObject());
            }
        }
        if ($sensorTypeObject instanceof AnalogSensorTypeInterface) {
            $sensorTypeObjectErrors = $this->performSensorReadingTypeValidation(
                $sensorTypeObject->getAnalogObject(),
                $sensorType
            );
            if (!empty($sensorTypeObjectErrors)) {
                $errors = [...$errors, $sensorTypeObjectErrors];
            } else {
                $this->saveReadingType($sensorTypeObject->getAnalogObject());
            }
        }

        return $errors;
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
