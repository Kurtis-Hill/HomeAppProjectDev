<?php


namespace App\DTOs\Factorys\CardDTOs;


use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\HomeAppSensorCore\Interfaces\SensorInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\AnalogSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\HumiditySensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\LatitudeSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\TemperatureSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;

abstract class AbstractCardDataFactory
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
        if (empty($sensorData)) {
            throw new \RuntimeException('Sensor type not recognised, the app needs updating to support the new feature');
        }

        return $sensorData;
    }

    abstract protected function setStandardSensorData(StandardReadingSensorInterface $sensorTypeObject, string $type, string $symbol = null): array;
}
