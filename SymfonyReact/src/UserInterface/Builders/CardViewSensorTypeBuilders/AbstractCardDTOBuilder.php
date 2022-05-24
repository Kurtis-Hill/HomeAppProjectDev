<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\UserInterface\DTO\Response\CardForms\StandardSensorTypeBoundaryViewFormDTO;
use App\UserInterface\DTO\Response\CardViewDTO\StandardCardViewDTO;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;

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
        $dateTime = $this->formatDateTime($cardData['humid_updatedAt']);

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
//        dd($cardData);
        $dateTime = $this->formatDateTime($cardData['lat_updatedAt']);

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
            throw new RuntimeException('Sensor type not recognised, the app needs updating to support the new feature');
        }

        return $sensorData;
    }

    private function setStandardSensorData(
        StandardReadingSensorInterface $sensorTypeObject,
        string $type,
        string $symbol = null
    ): StandardSensorTypeBoundaryViewFormDTO
    {
        return new StandardSensorTypeBoundaryViewFormDTO(
            $type,
            is_float($sensorTypeObject->getHighReading())
                ? number_format($sensorTypeObject->getHighReading(), 2)
                : $sensorTypeObject->getHighReading(),
            is_float($sensorTypeObject->getLowReading())
                ? number_format($sensorTypeObject->getLowReading(), 2)
                : $sensorTypeObject->getLowReading(),
            $sensorTypeObject->getConstRecord(),
            $symbol
        );
    }
}
