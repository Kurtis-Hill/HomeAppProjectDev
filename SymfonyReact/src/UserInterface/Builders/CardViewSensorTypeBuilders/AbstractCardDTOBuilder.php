<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\UserInterface\DTO\CardViewDTO\StandardCardViewDTO;
use App\UserInterface\DTO\UserViewReadingSensorTypeCardData\StandardSensorTypeViewFormDTO;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;

abstract class AbstractCardDTOBuilder
{
    protected function buildTemperatureSensorData(array $cardData): ?StandardCardViewDTO
    {
        if (empty($cardData['temp_tempID'])) {
            return null;
        }
        $dateTime = $this->formatDateTime($cardData['temp_updatedAt']);

        return new StandardCardViewDTO(
            Temperature::READING_TYPE,
            $cardData['temp_currentReading'],
            $cardData['temp_highTemp'],
            $cardData['temp_lowTemp'],
            $dateTime,
            Temperature::READING_SYMBOL,
        );
    }

    protected function buildHumiditySensorData($cardData): ?StandardCardViewDTO
    {
        if (empty($cardData['humid_humidID'])) {
            return null;
        }
        $dateTime = $this->formatDateTime($cardData['humid_updateAt']);

        return new StandardCardViewDTO(
            Humidity::READING_TYPE,
            $cardData['humid_currentReading'],
            $cardData['humid_highHumid'],
            $cardData['humid_lowHumid'],
            $dateTime,
            Humidity::READING_SYMBOL,
        );
    }

    protected function buildLatitudeSensorData($cardData): ?StandardCardViewDTO
    {
        if (empty($cardData['lat_latitudeID'])) {
            return null;
        }
        $dateTime = $this->formatDateTime($cardData['lat_time']);

        return new StandardCardViewDTO(
            Latitude::READING_TYPE,
            $cardData['lat_latitude'],
            $cardData['lat_highLatitude'],
            $cardData['lat_lowLatitude'],
            $dateTime,
        );
    }

    protected function buildAnalogSensorData($cardData): ?StandardCardViewDTO
    {
        if (empty($cardData['analog_analogID'])) {
            return null;
        }
        $dateTime = $this->formatDateTime($cardData['analog_updatedAt']);

        return new StandardCardViewDTO(
            Analog::READING_TYPE,
            $cardData['analog_analogReading'],
            $cardData['analog_highAnalog'],
            $cardData['analog_lowAnalog'],
            $dateTime,
        );
    }

    private function formatDateTime(DateTime $dateTime): string
    {
        return $dateTime->format('d-m-Y H:i:s');
    }

    #[ArrayShape([StandardCardViewDTO::class])]
    public function formatSensorTypeObjects(SensorTypeInterface $cardDTOData): array
    {
        if ($cardDTOData instanceof TemperatureSensorTypeInterface) {
            $sensorData[] = $this->setStandardSensorData($cardDTOData->getTempObject(), Temperature::READING_TYPE, Temperature::READING_SYMBOL);
        }
        if ($cardDTOData instanceof HumiditySensorTypeInterface) {
            $sensorData[] = $this->setStandardSensorData($cardDTOData->getHumidObject(), Humidity::READING_TYPE, Humidity::READING_SYMBOL);
        }
        if ($cardDTOData instanceof LatitudeSensorTypeInterface) {
            $sensorData[] = $this->setStandardSensorData($cardDTOData->getLatitudeObject(), Latitude::READING_TYPE);
        }
        if ($cardDTOData instanceof AnalogSensorTypeInterface) {
            $sensorData[] = $this->setStandardSensorData($cardDTOData->getAnalogObject(), Analog::READING_TYPE);
        }
//        if ($cardDTOData instanceof OnOffSensorTypeInterface) {
//            $sensorData[] = $this->setOnOffSensordata($cardDTOData->getPIRObject(), 'PIR');
//        }
        if (empty($sensorData)) {
            throw new \RuntimeException('Sensor type not recognised, the app needs updating to support the new feature');
        }

        return $sensorData;
    }


    #[ArrayShape([StandardSensorTypeViewFormDTO::class])]
    private function setStandardSensorData(
        StandardReadingSensorInterface $sensorTypeObject,
        string $type,
        string $symbol = null
    ): StandardSensorTypeViewFormDTO
    {
        return new StandardSensorTypeViewFormDTO(
            $type,
            is_float($sensorTypeObject->getHighReading())
                ? number_format($sensorTypeObject->getHighReading(), 2)
                : $sensorTypeObject->getHighReading(),
            is_float($sensorTypeObject->getLowReading())
                ? number_format($sensorTypeObject->getLowReading(), 2)
                : $sensorTypeObject->getLowReading(),
            $sensorTypeObject->getConstRecord(),
        );
    }
}
