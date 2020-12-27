<?php


namespace App\DTOs;


use App\HomeAppSensorCore\Interfaces\SensorTypes\AnalogSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\HumiditySensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\LatitudeSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\TemperatureSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardSensorInterface;

abstract class CardDTOAbstract
{
    protected function filterSensorTypes(StandardSensorTypeInterface $cardDTOData): void
    {
        if ($cardDTOData instanceof TemperatureSensorTypeInterface) {
            $this->setSensorData($cardDTOData->getTempObject(), 'Temperature');
        }
        if ($cardDTOData instanceof HumiditySensorTypeInterface) {
            $this->setSensorData($cardDTOData->getHumidObject(), 'Humidity');
        }
        if ($cardDTOData instanceof LatitudeSensorTypeInterface) {
            $this->setSensorData($cardDTOData->getLatitudeObject(), 'Latitude');
        }
        if ($cardDTOData instanceof AnalogSensorTypeInterface) {
            $this->setSensorData($cardDTOData->getAnalogObject(), 'Analog');
        }
    }

    abstract protected function setSensorData(StandardSensorInterface $sensorTypeObject, string $type): void;

    abstract protected function setCardViewData(StandardSensorTypeInterface $cardDTOData): void;

}
