<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders;

use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingTypeFactoryInterface;

class AbstractSensorReadingTypeBuilder
{
    protected SensorReadingTypeFactoryInterface $sensorReadingTypeFactory;

    public function __construct(SensorReadingTypeFactoryInterface $readingTypeFactory)
    {
        $this->sensorReadingTypeFactory = $readingTypeFactory;
    }

    public function buildTemperatureSensor(TemperatureSensorTypeInterface $temperatureSensorType): void
    {
        if (!$temperatureSensorType instanceof SensorTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $temperatureSensor = new Temperature();
        $temperatureSensor->setCurrentReading(10);
        $temperatureSensor->setHighReading($temperatureSensorType->getMaxTemperature());
        $temperatureSensor->setLowReading($temperatureSensorType->getMinTemperature());
        $temperatureSensor->setUpdatedAt();
        $temperatureSensor->setSensorObject($temperatureSensorType->getSensorObject());

        $temperatureSensorType->setTempObject($temperatureSensor);

        $readingTypeRepository = $this->sensorReadingTypeFactory->getSensorReadingTypeRepository($temperatureSensor::class);
        $readingTypeRepository->persist($temperatureSensor);
    }

    public function buildHumiditySensor(HumiditySensorTypeInterface $humiditySensorType): void
    {
        if (!$humiditySensorType instanceof SensorTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $humiditySensor = new Humidity();
        $humiditySensor->setCurrentReading(10);
        $humiditySensor->setHighReading($humiditySensorType->getMaxHumidity());
        $humiditySensor->setLowReading($humiditySensorType->getMinHumidity());
        $humiditySensor->setUpdatedAt();
        $humiditySensor->setSensorObject($humiditySensorType->getSensorObject());

        $humiditySensorType->setHumidObject($humiditySensor);

        $readingTypeRepository = $this->sensorReadingTypeFactory->getSensorReadingTypeRepository($humiditySensor::class);
        $readingTypeRepository->persist($humiditySensor);
    }

    public function buildLatitudeSensor(LatitudeSensorTypeInterface $latitudeSensorType): void
    {
        if (!$latitudeSensorType instanceof SensorTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $latitudeSensor = new Latitude();
        $latitudeSensor->setCurrentReading(10);
        $latitudeSensor->setHighReading($latitudeSensorType->getMaxLatitude());
        $latitudeSensor->setLowReading($latitudeSensorType->getMinLatitude());
        $latitudeSensor->setUpdatedAt();
        $latitudeSensor->setSensorObject($latitudeSensorType->getSensorObject());

        $latitudeSensorType->setLatitudeObject($latitudeSensor);

        $readingTypeRepository = $this->sensorReadingTypeFactory->getSensorReadingTypeRepository($latitudeSensor::class);
        $readingTypeRepository->persist($latitudeSensor);
    }

    public function buildAnalogSensor(AnalogSensorTypeInterface $analogSensorType): void
    {
        if (!$analogSensorType instanceof SensorTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $analogSensor = new Analog();
        $analogSensor->setCurrentReading(10);
        $analogSensor->setHighReading($analogSensorType->getMaxAnalog());
        $analogSensor->setLowReading($analogSensorType->getMinAnalog());
        $analogSensor->setUpdatedAt();
        $analogSensor->setSensorObject($analogSensorType->getSensorObject());

        $analogSensorType->setAnalogObject($analogSensor);

//        $readingTypeRepository = $this->sensorReadingTypeFactory->getSensorReadingTypeRepository($analogSensor::class);
//        $readingTypeRepository->persist($analogSensor);
    }

    protected function setSensorObject(SensorTypeInterface $sensorType, Sensor $sensor): void
    {
        $sensorType->setSensorObject($sensor);
    }
}
