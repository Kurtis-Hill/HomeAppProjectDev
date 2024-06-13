<?php

namespace App\Services\Sensor\NewReadingType;

use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\Sensor;
use App\Exceptions\Sensor\ReadingTypeNotFoundException;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Exceptions\Sensor\SensorTypeException;
use App\Factories\Sensor\SensorType\SensorTypeRepositoryFactory;
use App\Factories\Sensor\SensorTypeCreationFactory\SensorTypeCreationFactory;
use App\Services\Sensor\SensorReadingTypesValidator\SensorReadingTypesValidatorInterface;
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

    /**
     * @throws SensorTypeException|SensorReadingTypeRepositoryFactoryException
     * @throws ReadingTypeNotFoundException
     */
    #[ArrayShape([Temperature::class|Humidity::class|Latitude::class|Analog::class|Relay::class|Motion::class])]
    public function handleSensorReadingTypeCreation(Sensor $sensor): array
    {
        $createdSensorReadingTypes = $this->createNewSensorReadingTypeData($sensor);
        if (empty($createdSensorReadingTypes)) {
            throw new ReadingTypeNotFoundException(
                'No reading types were found for this sensor'
            );
        }

        return $createdSensorReadingTypes;
    }

    /**
     * @throws SensorTypeException|SensorReadingTypeRepositoryFactoryException
     */
    #[ArrayShape([Temperature::class|Humidity::class|Latitude::class|Analog::class|Relay::class|Motion::class])]
    private function createNewSensorReadingTypeData(Sensor $sensor): array
    {
        $sensorType = $sensor->getSensorTypeObject()::getReadingTypeName();

        $sensorReadingCreationService = $this->readingTypeCreationFactory
            ->getSensorReadingTypeBuilder(
                $sensorType
            );

        return $sensorReadingCreationService->buildNewSensorTypeObjects($sensor);
    }

//    private function validateSensorReadingTypeData(SensorTypeInterface $sensorTypeObject): array
//    {
//        return $this->sensorReadingTypesValidatorService->validateSensorReadingTypeObjectsBySensorTypeObject($sensorTypeObject);
//    }
//
//    /**
//     * @throws SensorTypeException
//     */
//    private function persistSensorTypeObjects(SensorTypeInterface $sensorTypeObject): void
//    {
//        $sensorTypeObjectAsString = $sensorTypeObject->getReadingTypeName();
//        $sensorTypeRepository = $this->sensorTypeFactory->getSensorTypeRepository($sensorTypeObjectAsString);
////        $sensorTypeRepository->persist($sensorTypeObject);
//    }
}
