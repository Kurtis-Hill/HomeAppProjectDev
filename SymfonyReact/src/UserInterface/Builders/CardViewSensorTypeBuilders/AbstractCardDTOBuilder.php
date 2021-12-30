<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\UserInterface\DTO\CardViewDTO\StandardCardViewDTO;
use DateTime;
use JetBrains\PhpStorm\Pure;

abstract class AbstractCardDTOBuilder
{
    #[Pure]
    protected function buildTemperatureSensorData(array $cardData): StandardCardViewDTO
    {
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

    #[Pure]
    protected function buildHumiditySensorData($cardData): StandardCardViewDTO
    {
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

    #[Pure]
    protected function buildLatitudeSensorData($cardData): StandardCardViewDTO
    {
        $dateTime = $this->formatDateTime($cardData['lat_time']);

        return new StandardCardViewDTO(
            Latitude::READING_TYPE,
            $cardData['lat_latitude'],
            $cardData['lat_highLatitude'],
            $cardData['lat_lowLatitude'],
            $dateTime,
        );
    }

    #[Pure]
    protected function buildAnalogSensorData($cardData): StandardCardViewDTO
    {
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
