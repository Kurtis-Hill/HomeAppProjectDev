<?php

namespace App\Sensors\SensorServices\NewReadingType;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\ORMFactories\SensorType\SensorTypeRepositoryFactory;
use App\Sensors\Factories\SensorTypeCreationFactory\SensorTypeCreationFactory;
use App\Sensors\SensorServices\SensorReadingTypesValidator\SensorReadingTypesValidatorInterface;
use Doctrine\ORM\Exception\ORMException;

class ReadingTypeCreationFacade implements ReadingTypeCreationInterface
{
    private SensorTypeRepositoryFactory $sensorTypeFactory;

    private SensorTypeCreationFactory $readingTypeCreationFactory;

    private SensorReadingTypesValidatorInterface $sensorReadingTypesValidatorService;

    public function __construct(
        SensorTypeRepositoryFactory $sensorTypeFactory,
        SensorTypeCreationFactory $readingTypeCreationFactory,
        SensorReadingTypesValidatorInterface $sensorReadingTypesValidator,
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
