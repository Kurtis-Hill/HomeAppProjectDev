<?php

namespace App\DTOs\CardDTOs\Builders\SensorTypeBuilders;

use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;

abstract class AbstractSensorTypeCardDataBuilder
{
    /**
     * @param SensorInterface $cardDTOData
     * @return array
     */
    protected function filterSensorTypesAndGetData(SensorInterface $cardDTOData): array
    {
        if ($cardDTOData instanceof TemperatureSensorTypeInterface) {
            $sensorData[] = $this->setStandardSensorData($cardDTOData->getTempObject(), 'temperature', Temperature::READING_SYMBOL);
        }
        if ($cardDTOData instanceof HumiditySensorTypeInterface) {
            $sensorData[] = $this->setStandardSensorData($cardDTOData->getHumidObject(), 'humidity', Humidity::READING_SYMBOL);
        }
        if ($cardDTOData instanceof LatitudeSensorTypeInterface) {
            $sensorData[] = $this->setStandardSensorData($cardDTOData->getLatitudeObject(), 'latitude');
        }
        if ($cardDTOData instanceof AnalogSensorTypeInterface) {
            $sensorData[] = $this->setStandardSensorData($cardDTOData->getAnalogObject(), 'analog');
        }
//        if ($cardDTOData instanceof OnOffSensorTypeInterface) {
//            $sensorData[] = $this->setOnOffSensordata($cardDTOData->getPIRObject(), 'PIR');
//        }
        if (empty($sensorData)) {
            throw new \RuntimeException('Sensor type not recognised, the app needs updating to support the new feature');
        }

        return $sensorData;
    }

    /**
     * @param StandardReadingSensorInterface $sensorTypeObject
     * @param string $type
     * @param string|null $symbol
     * @return array
     */
    abstract protected function setStandardSensorData(StandardReadingSensorInterface $sensorTypeObject, string $type, string $symbol = null): array;
}
