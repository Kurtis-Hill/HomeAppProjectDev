<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor\ReadingTypeCreation;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Exceptions\SensorTypeBuilderFailureException;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingTypeFactoryInterface;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorType\SensorTypeFactoryInterface;
use App\ESPDeviceSensor\Factories\ReadingTypeCreationFactory\ReadingTypeCreationFactory;
use Doctrine\ORM\ORMException;

class ReadingTypeCreationServiceTwo implements SensorReadingTypeCreationInterface
{
    private SensorReadingTypeFactoryInterface $sensorReadingTypeFactory;

    private SensorTypeFactoryInterface $sensorTypeFactory;

    private ReadingTypeCreationFactory $readingTypeCreationFactory;

    public function __construct(
        SensorReadingTypeFactoryInterface $sensorReadingTypeFactory,
        SensorTypeFactoryInterface $sensorTypeFactory,
        ReadingTypeCreationFactory $readingTypeCreationFactory,
    ) {
        $this->sensorReadingTypeFactory = $sensorReadingTypeFactory;
        $this->sensorTypeFactory = $sensorTypeFactory;
        $this->readingTypeCreationFactory = $readingTypeCreationFactory;
    }

    public function handleSensorReadingTypeCreation(Sensor $sensor): array
    {
        $errors = [];

        try {
            $errors[] = $this->createNewSensorReadingTypeData($sensor);
        } catch (ORMException $e) {
            $errors[] = 'Failed to save sensor reading type data';
        } catch (SensorTypeException $e) {
            $errors[] = $e->getMessage();
        }

        return $errors;
    }

    private function createNewSensorReadingTypeData(Sensor $sensor)
    {
        $sensorReadingCreationService = $this->readingTypeCreationFactory
            ->getSensorReadingTypeBuilder(
                $sensor->getSensorTypeObject()->getSensorType()
            );

        try {
            $sensorTypeObject = $sensorReadingCreationService->buildReadingTypeObjects($sensor);
        } catch (SensorTypeBuilderFailureException $e) {
            return $e->getMessage();
        }

        $sensorTypeString = $sensorTypeObject::class;

        $sensorTypeRepository = $this->sensorTypeFactory->getSensorTypeRepository($sensorTypeString);

        $sensorTypeRepository->persist($sensorTypeObject);
        $sensorTypeRepository->flush();
    }
}
