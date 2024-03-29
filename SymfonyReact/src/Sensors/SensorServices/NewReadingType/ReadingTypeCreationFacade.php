<?php

namespace App\Sensors\SensorServices\NewReadingType;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\SensorType\SensorTypeRepositoryFactory;
use App\Sensors\Factories\SensorTypeCreationFactory\SensorTypeCreationFactory;
use App\Sensors\SensorServices\SensorReadingTypesValidator\SensorReadingTypesValidatorInterface;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

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

    #[ArrayShape(['string'])]
    public function handleSensorReadingTypeCreation(Sensor $sensor): array
    {
        try {
            $sensorTypeObject = $this->createNewSensorReadingTypeData($sensor);
            $validationErrors = $this->validateSensorReadingTypeData($sensorTypeObject);
        } catch (SensorTypeException | SensorReadingTypeRepositoryFactoryException $e) {
            return [$e->getMessage()];
        } catch (ORMException) {
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

        return $sensorReadingCreationService->buildNewSensorTypeObjects($sensor);
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
        $sensorTypeObjectAsString = $sensorTypeObject->getReadingTypeName();
        $sensorTypeRepository = $this->sensorTypeFactory->getSensorTypeRepository($sensorTypeObjectAsString);
        $sensorTypeRepository->persist($sensorTypeObject);
    }
}
