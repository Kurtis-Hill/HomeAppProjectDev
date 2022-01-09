<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator;

use App\Common\Traits\ValidatorProcessorTrait;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingTypeFactoryInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
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
                $this->saveSensorData($sensorTypeObject->getTempObject());
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
                $this->saveSensorData($sensorTypeObject->getHumidObject());
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
                $this->saveSensorData($sensorTypeObject->getLatitudeObject());
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
                $this->saveSensorData($sensorTypeObject->getAnalogObject());
            }
        }
//dd($errors, 'er');
        return $errors;
    }
    
    #[ArrayShape(['string'])]
    public function validateSensorReadingTypeObject(AllSensorReadingTypeInterface $sensorReadingTypeObject, string $sensorType): array
    {
        $validationErrors = $this->performSensorReadingTypeValidation($sensorReadingTypeObject, $sensorType);
//dd($validationErrors);
        if (empty($validationErrors)) {
            $this->saveSensorData($sensorReadingTypeObject);
        }

        return $validationErrors;
    }

    private function performSensorReadingTypeValidation(
        AllSensorReadingTypeInterface $sensorReadingType,
        ?string $sensorTypeName = null
    ): array {
//        $highLowCheck = $this->highLowCheckConstraint();

//        dd($highLowCheck);
        $validationErrors = $this->validator->validate(
            $sensorReadingType,
//            [$highLowCheck],
            null,
            $sensorTypeName
        );

//        dd('here', $validationErrors, $sensorReadingType);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
//            dd('[resert');
//            dd($this->getValidationErrorAsArray($validationErrors));
            return $this->getValidationErrorAsArray($validationErrors);
        }

        $this->saveSensorData($sensorReadingType);

        return [];
    }

    private function saveSensorData(AllSensorReadingTypeInterface $sensorType): void
    {
        $repository = $this->sensorReadingTypeFactory
            ->getSensorReadingTypeRepository($sensorType->getSensorTypeName());

        $repository->persist($sensorType);
        $repository->flush();
    }

    public function validate($object, ExecutionContextInterface $context, $payload)
    {

//        dd($object, $payload);
        return new Callback(function($object, ExecutionContextInterface $context) {
//        dd('s');
            $lowReading = $context->getRoot()->getData()->getLowReading();
            $highReading = $context->getRoot()->getData()->getHighReading();
            $readingType = $context->getRoot()->getData();

//            if ($readingType instanceof StandardReadingSensorInterface) {
                if ($highReading < $lowReading) {
                    $context
                        ->buildViolation('High reading for ' . $readingType->getSensorTypeName() . ' cannot be lower than low reading')
                        ->addViolation();
                }
//            } else {
//                $context
//                    ->buildViolation('App needs updating to support this sensor type')
//                    ->addViolation();
//            }
        });
    }
}
