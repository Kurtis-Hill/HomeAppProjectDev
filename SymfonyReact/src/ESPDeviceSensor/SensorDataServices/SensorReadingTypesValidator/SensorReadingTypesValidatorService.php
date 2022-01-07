<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator;

use App\Common\Traits\ValidatorProcessorTrait;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingTypeFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SensorReadingTypesValidatorService implements SensorReadingTypesValidatorServiceInterface
{
    use ValidatorProcessorTrait;

    private SensorReadingTypeFactoryInterface $sensorReadingTypeFactory;

    private ValidatorInterface $validator;

    public function __construct(SensorReadingTypeFactoryInterface $readingTypeFactory, ValidatorInterface $validator,)
    {
        $this->sensorReadingTypeFactory = $readingTypeFactory;
        $this->validator = $validator;
    }

    public function validateSensorTypeObject(SensorTypeInterface $sensorTypeObject): array
    {
//        dd($sensorTypeObject);
        $sensorType = $sensorTypeObject->getSensorTypeName();

        $errors = [];
        if ($sensorTypeObject instanceof TemperatureSensorTypeInterface) {
            $sensorTypeObjectErrors = $this->performSensorReadingTypeValidation(
                $sensorTypeObject->getTempObject(),
                $sensorType
            );
            if (!empty($sensorTypeObjectErrors)) {
                $errors = [...$errors, $sensorTypeObjectErrors];
            }
        }
        if ($sensorTypeObject instanceof HumiditySensorTypeInterface) {
            $sensorTypeObjectErrors = $this->performSensorReadingTypeValidation(
                $sensorTypeObject->getHumidObject(),
                $sensorType
            );
//            dd($sensorTypeObjectErrors, $sensorTypeObject->getHumidObject());
            if (!empty($sensorTypeObjectErrors)) {
                $errors = [...$errors, $sensorTypeObjectErrors];
            }
        }
        if ($sensorTypeObject instanceof LatitudeSensorTypeInterface) {
            $sensorTypeObjectErrors = $this->performSensorReadingTypeValidation(
                $sensorTypeObject->getLatitudeObject(),
                $sensorType
            );
            if (!empty($sensorTypeObjectErrors)) {
                $errors = [...$errors, $sensorTypeObjectErrors];
            }
        }
        if ($sensorTypeObject instanceof AnalogSensorTypeInterface) {
            $sensorTypeObjectErrors = $this->performSensorReadingTypeValidation(
                $sensorTypeObject->getAnalogObject(),
                $sensorType
            );
            if (!empty($sensorTypeObjectErrors)) {
                $errors = [...$errors, $sensorTypeObjectErrors];
            }
        }

        return $errors;
    }
    
    public function validateSensorReadingTypeObject(AllSensorReadingTypeInterface $sensorReadingTypeObject, string $sensorType): array
    {
        $validationErrors = $this->validator->validate($sensorReadingTypeObject, null, $sensorType);
//dd($validationErrors, $sensorReadingTypeObject, $sensorType);
        return $this->getValidationErrorAsArray($validationErrors);
    }

    private function performSensorReadingTypeValidation(
        AllSensorReadingTypeInterface $sensorReadingType,
        ?string $sensorTypeName = null
    ): array {
        $validationErrors = $this->validator->validate(
            $sensorReadingType,
            null,
            $sensorTypeName
        );
//        dd($validationErrors, $sensorReadingType);

        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->getValidationErrorAsArray($validationErrors);
        }

//        if (count($validationErrors) > 0) {
//            foreach ($validationErrors as $error) {
//                $errors[] = $error->getMessage();
//            }
//        } else {
        $this->persistSensorData($sensorReadingType);
//        }

        return [];
    }

    private function persistSensorData(AllSensorReadingTypeInterface $sensorType): void
    {
        $repository = $this->sensorReadingTypeFactory
            ->getSensorReadingTypeRepository($sensorType->getSensorTypeName());
        $repository->persist($sensorType);
        $repository->flush();
    }
}
