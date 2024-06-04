<?php

namespace App\Sensors\SensorServices\NewReadingType;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotFoundException;
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
