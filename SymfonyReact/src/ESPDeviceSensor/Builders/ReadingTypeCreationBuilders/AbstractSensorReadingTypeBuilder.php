<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders;

use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;

class AbstractSensorReadingTypeBuilder
{
    public function buildTemperatureSensor(TemperatureSensorTypeInterface $temperatureSensorType): void
    {
        $temperatureSensor = new Temperature();
        $temperatureSensor->setCurrentReading(10);
        $temperatureSensor->setHighReading($temperatureSensorType->getMaxTemperature());
        $temperatureSensor->setLowReading($temperatureSensorType->getMinTemperature());
        $temperatureSensor->setTime();

        $temperatureSensorType->setTempObject($temperatureSensor);
    }

    public function buildHumiditySensor(HumiditySensorTypeInterface $humiditySensorType): void
    {
        $humiditySensor = new Humidity();
        $humiditySensor->setCurrentReading(10);
        $humiditySensor->setHighReading($humiditySensorType->getMaxHumidity());
        $humiditySensor->setLowReading($humiditySensorType->getMinHumidity());
        $humiditySensor->setTime();

        $humiditySensorType->setHumidObject($humiditySensor);
    }

    public function buildLatitudeSensor(LatitudeSensorTypeInterface $latitudeSensorType): void
    {
        $latitudeSensor = new Latitude();
        $latitudeSensor->setCurrentReading(10);
        $latitudeSensor->setHighReading($latitudeSensorType->getMaxLatitude());
        $latitudeSensor->setLowReading($latitudeSensorType->getMinLatitude());
        $latitudeSensor->setTime();

        $latitudeSensorType->setLatitudeObject($latitudeSensor);
    }

    public function buildAnalogSensor(AnalogSensorTypeInterface $analogSensorType): void
    {
        $latitudeSensor = new Latitude();
        $latitudeSensor->setCurrentReading(10);
        $latitudeSensor->setHighReading($analogSensorType->getMaxAnalog());
        $latitudeSensor->setLowReading($analogSensorType->getMinAnalog());
        $latitudeSensor->setTime();

        $analogSensorType->setAnalogObject($latitudeSensor);
    }
}
