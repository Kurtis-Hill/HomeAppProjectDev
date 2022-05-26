<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\ORMFactories\SensorReadingType\SensorReadingTypeRepositoryFactoryInterface;

class AbstractReadingTypeBuilder
{
    protected SensorReadingTypeRepositoryFactoryInterface $sensorReadingTypeRepositoryFactory;

    public function __construct(SensorReadingTypeRepositoryFactoryInterface $readingTypeFactory)
    {
        $this->sensorReadingTypeRepositoryFactory = $readingTypeFactory;
    }

//    /**
//     * @throws SensorReadingTypeRepositoryFactoryException
//     * @throws SensorTypeException
//     */
//    public function buildTemperatureSensor(TemperatureSensorTypeInterface $temperatureSensorType, int|float $currentReading = 10): void
//    {
//        if (!$temperatureSensorType instanceof SensorTypeInterface) {
//            throw new SensorTypeException(
//                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
//            );
//        }
//        $temperatureSensor = new Temperature();
//        $temperatureSensor->setCurrentReading($currentReading);
//        $temperatureSensor->setHighReading($temperatureSensorType->getMaxTemperature());
//        $temperatureSensor->setLowReading($temperatureSensorType->getMinTemperature());
//        $temperatureSensor->setUpdatedAt();
//        $temperatureSensor->setSensorObject($temperatureSensorType->getSensorObject());
//
//        $temperatureSensorType->setTempObject($temperatureSensor);
//
//        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($temperatureSensor->getReadingType());
//        $readingTypeRepository->persist($temperatureSensor);
//    }

//    /**
//     * @throws SensorReadingTypeRepositoryFactoryException
//     * @throws SensorTypeException
//     */
//    public function buildHumiditySensor(HumiditySensorTypeInterface $humiditySensorType, int|float $currentReading = 10): void
//    {
//        if (!$humiditySensorType instanceof SensorTypeInterface) {
//            throw new SensorTypeException(
//                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
//            );
//        }
//        $humiditySensor = new Humidity();
//        $humiditySensor->setCurrentReading($currentReading);
//        $humiditySensor->setHighReading($humiditySensorType->getMaxHumidity());
//        $humiditySensor->setLowReading($humiditySensorType->getMinHumidity());
//        $humiditySensor->setUpdatedAt();
//        $humiditySensor->setSensorObject($humiditySensorType->getSensorObject());
//
//        $humiditySensorType->setHumidObject($humiditySensor);
//
//        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($humiditySensor->getReadingType());
//        $readingTypeRepository->persist($humiditySensor);
//    }

    /**
     * @throws SensorReadingTypeRepositoryFactoryException
     * @throws SensorTypeException
     */
    public function buildLatitudeSensor(LatitudeSensorTypeInterface $latitudeSensorType, int|float $currentReading = 10): void
    {
//        if (!$latitudeSensorType instanceof SensorTypeInterface) {
//            throw new SensorTypeException(
//                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
//            );
//        }
//        $latitudeSensor = new Latitude();
//        $latitudeSensor->setCurrentReading($currentReading);
//        $latitudeSensor->setHighReading($latitudeSensorType->getMaxLatitude());
//        $latitudeSensor->setLowReading($latitudeSensorType->getMinLatitude());
//        $latitudeSensor->setUpdatedAt();
//        $latitudeSensor->setSensorObject($latitudeSensorType->getSensorObject());
//
//        $latitudeSensorType->setLatitudeObject($latitudeSensor);
//
//        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($latitudeSensor->getReadingType());
//        $readingTypeRepository->persist($latitudeSensor);
    }

    /**
     * @throws SensorTypeException
     */
    public function buildAnalogSensor(AnalogSensorTypeInterface $analogSensorType, int|float $currentReading = 1000): void
    {
        if (!$analogSensorType instanceof SensorTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $analogSensor = new Analog();
        $analogSensor->setCurrentReading($currentReading);
        $analogSensor->setHighReading($analogSensorType->getMaxAnalog());
        $analogSensor->setLowReading($analogSensorType->getMinAnalog());
        $analogSensor->setUpdatedAt();
        $analogSensor->setSensorObject($analogSensorType->getSensorObject());

        $analogSensorType->setAnalogObject($analogSensor);
    }

    protected function setSensorObject(SensorTypeInterface $sensorType, Sensor $sensor): void
    {
        $sensorType->setSensorObject($sensor);
    }
}
