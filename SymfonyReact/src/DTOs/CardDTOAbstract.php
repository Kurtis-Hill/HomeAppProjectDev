<?php


namespace App\DTOs;


use App\HomeAppSensorCore\Interfaces\SensorTypes\AnalogSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\HumiditySensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\LatitudeSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\TemperatureSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;

abstract class CardDTOAbstract
{
    protected function filterSensorTypes(StandardSensorTypeInterface $cardDTOData): void
    {
        $processed = false;
        if ($cardDTOData instanceof TemperatureSensorTypeInterface) {
            $this->setSensorData($cardDTOData->getTempObject(), 'temperature', '°C');
            $processed = true;
        }
        if ($cardDTOData instanceof HumiditySensorTypeInterface) {
            $this->setSensorData($cardDTOData->getHumidObject(), 'humidity', '%');
            $processed = true;
        }
        if ($cardDTOData instanceof LatitudeSensorTypeInterface) {
            $this->setSensorData($cardDTOData->getLatitudeObject(), 'latitude');
            $processed = true;
        }
        if ($cardDTOData instanceof AnalogSensorTypeInterface) {
            $this->setSensorData($cardDTOData->getAnalogObject(), 'analog');
            $processed = true;
        }

        if ($processed === false) {
            throw new \RuntimeException('Sensor type not recognised, the app needs updating to support the new feature');
        }
    }

    abstract protected function setSensorData(StandardReadingSensorInterface $sensorTypeObject, string $type, string $symbol = null): void;

    abstract protected function setCardViewData(StandardSensorTypeInterface $cardDTOData): void;

}
