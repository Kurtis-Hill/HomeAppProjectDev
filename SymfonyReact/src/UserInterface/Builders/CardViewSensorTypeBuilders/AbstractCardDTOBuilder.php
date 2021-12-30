<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\UserInterface\DTO\CardViewDTO\StandardCardViewDTO;
use DateTime;

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
}
