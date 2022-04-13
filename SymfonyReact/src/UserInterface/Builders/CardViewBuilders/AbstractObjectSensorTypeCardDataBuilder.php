<?php

namespace App\UserInterface\Builders\CardViewBuilders;

use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;

abstract class AbstractObjectSensorTypeCardDataBuilder
{
    protected function filterSensorTypesAndGetData(SensorTypeInterface $cardDTOData): array
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
