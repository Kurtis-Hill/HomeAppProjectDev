<?php

namespace App\Sensors\SensorDataServices\NewReadingType;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\ORMFactories\SensorType\SensorTypeRepositoryFactoryInterface;
use App\Sensors\Factories\SensorTypeCreationFactory\SensorTypeCreationFactory;
use App\Sensors\SensorDataServices\SensorReadingTypesValidator\SensorReadingTypesValidatorServiceInterface;
use Doctrine\ORM\Exception\ORMException;

class ReadingTypeCreationService implements SensorReadingTypeCreationInterface
{
    private SensorTypeRepositoryFactoryInterface $sensorTypeFactory;

    private SensorTypeCreationFactory $readingTypeCreationFactory;

    private SensorReadingTypesValidatorServiceInterface $sensorReadingTypesValidatorService;

    public function __construct(
        SensorTypeRepositoryFactoryInterface $sensorTypeFactory,
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
        } catch (SensorTypeException | SensorReadingTypeRepositoryFactoryException $e) {
//            dd($e);
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
     * @throws SensorTypeException|SensorReadingTypeRepositoryFactoryException
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
        $sensorTypeObjectAsString = $sensorTypeObject->getSensorTypeName();
        $sensorTypeRepository = $this->sensorTypeFactory->getSensorTypeRepository($sensorTypeObjectAsString);
        $sensorTypeRepository->persist($sensorTypeObject);
    }
}
