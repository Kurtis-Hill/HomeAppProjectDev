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
use App\Sensors\Exceptions\SensorTypeNotFoundException;
use App\UserInterface\DTO\Response\CardForms\StandardSensorTypeBoundaryViewFormDTO;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;

abstract class AbstractCardDTOBuilder
{
    /**
     * @throws SensorTypeNotFoundException
     */
    #[ArrayShape([StandardCardViewReadingResponseDTO::class])]
    public function formatSensorTypeObjectsByReadingType(SensorTypeInterface $cardDTOData): array
    {
        if ($cardDTOData instanceof TemperatureSensorTypeInterface) {
            $sensorData[] = $this->setStandardSensorData($cardDTOData->getTempObject(), Temperature::getReadingTypeName(), Temperature::READING_SYMBOL);
        }
        if ($cardDTOData instanceof HumiditySensorTypeInterface) {
            $sensorData[] = $this->setStandardSensorData($cardDTOData->getHumidObject(), Humidity::getReadingTypeName(), Humidity::READING_SYMBOL);
        }
        if ($cardDTOData instanceof LatitudeSensorTypeInterface) {
            $sensorData[] = $this->setStandardSensorData($cardDTOData->getLatitudeObject(), Latitude::getReadingTypeName());
        }
        if ($cardDTOData instanceof AnalogSensorTypeInterface) {
            $sensorData[] = $this->setStandardSensorData($cardDTOData->getAnalogObject(), Analog::READING_TYPE);
        }
//        if ($cardDTOData instanceof OnOffSensorTypeInterface) {
//            $sensorData[] = $this->setOnOffSensordata($cardDTOData->getPIRObject(), 'PIR');
//        }
        if (empty($sensorData)) {
            throw new SensorTypeNotFoundException('Sensor Type Not Found');
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
