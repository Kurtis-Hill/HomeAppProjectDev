<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor\ReadingTypeCreation;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\SensorReadingTypeValidationException;
use App\ESPDeviceSensor\Exceptions\SensorTypeBuilderFailureException;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorType\SensorTypeFactoryInterface;
use App\ESPDeviceSensor\Factories\ReadingTypeCreationFactory\ReadingTypeCreationFactory;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator\SensorReadingTypesValidatorServiceInterface;
use Doctrine\ORM\ORMException;

class ReadingTypeCreationServiceTwo implements SensorReadingTypeCreationInterface
{
    private SensorTypeFactoryInterface $sensorTypeFactory;

    private ReadingTypeCreationFactory $readingTypeCreationFactory;

    private SensorReadingTypesValidatorServiceInterface $sensorReadingTypesValidatorService;

    public function __construct(
        SensorTypeFactoryInterface $sensorTypeFactory,
        ReadingTypeCreationFactory $readingTypeCreationFactory,
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
        } catch (SensorTypeException | SensorTypeBuilderFailureException $e) {
            return [$e->getMessage()];
        } catch (ORMException $e) {
           return ['Failed to create sensor reading types'];
        } catch (\Exception $e) {
            dd($e);
        }

        $validationErrors = $this->validateSensorReadingTypeData($sensorTypeObject);

        if (empty($validationErrors)) {
            $this->saveSensorTypeObjects($sensorTypeObject);
        }

//        dd($errors);
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

        return $sensorReadingCreationService->buildReadingTypeObjects($sensor);
    }

    private function validateSensorReadingTypeData(SensorTypeInterface $sensorTypeObject): array
    {
        return $this->sensorReadingTypesValidatorService->validateReadingTypeObjects($sensorTypeObject);

//        if (!empty($validationErrors)) {
////            dd($validationErrors);
//            throw new SensorReadingTypeValidationException($validationErrors);
//        }
    }

    /**
     * @throws ORMException|SensorTypeException
     */
    private function saveSensorTypeObjects(SensorTypeInterface $sensorTypeObject): void
    {
//        dd($sensorTypeObject);
        $sensorTypeObjectAsString = $sensorTypeObject::class;
        $sensorTypeRepository = $this->sensorTypeFactory->getSensorTypeRepository($sensorTypeObjectAsString);
        $sensorTypeRepository->persist($sensorTypeObject);
//        dd($sensorTypeRepository->seePersistList());
//        $sensorTypeRepository->seePersistList(), persist($sensorTypeObject);
        //@DEV remove flush
        $sensorTypeRepository->flush();
    }
}
