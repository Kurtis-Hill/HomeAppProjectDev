<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor\ReadingTypeCreation;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorType\SensorTypeRepositroyFactoryInterface;
use App\ESPDeviceSensor\Factories\SensorTypeCreationFactory\SensorTypeCreationFactory;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator\SensorReadingTypesValidatorServiceInterface;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use Doctrine\ORM\ORMException;

class ReadingTypeCreationService implements SensorReadingTypeCreationInterface
{
    private SensorTypeRepositroyFactoryInterface $sensorTypeFactory;

    private SensorTypeCreationFactory $readingTypeCreationFactory;

    private SensorReadingTypesValidatorServiceInterface $sensorReadingTypesValidatorService;

    public function __construct(
        SensorTypeRepositroyFactoryInterface $sensorTypeFactory,
        SensorTypeCreationFactory $readingTypeCreationFactory,
        SensorReadingTypesValidatorServiceInterface $sensorReadingTypesValidator,
    ) {
        $this->sensorTypeFactory = $sensorTypeFactory;
        $this->readingTypeCreationFactory = $readingTypeCreationFactory;
        $this->sensorReadingTypesValidatorService = $sensorReadingTypesValidator;
    }

    public function handleSensorReadingTypeCreation(Sensor $sensor): array
    {
        try {
            $sensorTypeObject = $this->createNewSensorReadingTypeData($sensor);
            $validationErrors = $this->validateSensorReadingTypeData($sensorTypeObject);
        } catch (SensorTypeException | SensorTypeBuilderFailureException $e) {
            return [$e->getMessage()];
        } catch (ORMException $e) {
           return ['Failed to create sensor reading types'];
        }

        if (empty($validationErrors)) {
            try {
                $this->persistSensorTypeObjects($sensorTypeObject);
            } catch (SensorTypeException $e) {
                return [$e->getMessage()];
            }
        }

        return $validationErrors;
    }

    /**
     * @throws SensorTypeBuilderFailureException
     * @throws SensorTypeException
     */
    private function createNewSensorReadingTypeData(Sensor $sensor): SensorTypeInterface
    {
        $sensorType = $sensor->getSensorTypeObject()->getSensorType();

        $sensorReadingCreationService = $this->readingTypeCreationFactory
            ->getSensorReadingTypeBuilder(
                $sensorType
            );

        return $sensorReadingCreationService->buildNewReadingTypeObjects($sensor);
    }

    private function validateSensorReadingTypeData(SensorTypeInterface $sensorTypeObject): array
    {
        return $this->sensorReadingTypesValidatorService->validateSensorReadingTypeObjectsBySensorTypeObject($sensorTypeObject);
    }

    /**
     * @throws SensorTypeException
     */
    private function persistSensorTypeObjects(SensorTypeInterface $sensorTypeObject): void
    {
        $sensorTypeObjectAsString = $sensorTypeObject::class;
        $sensorTypeRepository = $this->sensorTypeFactory->getSensorTypeRepository($sensorTypeObjectAsString);
        $sensorTypeRepository->persist($sensorTypeObject);
    }
}
