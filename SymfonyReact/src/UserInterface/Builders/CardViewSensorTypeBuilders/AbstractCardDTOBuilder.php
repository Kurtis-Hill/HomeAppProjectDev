<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Sensors\Exceptions\SensorTypeNotFoundException;
use App\UserInterface\DTO\Response\CardForms\StandardSensorTypeBoundaryViewFormDTO;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

abstract class AbstractCardDTOBuilder
{
    /**
     * @throws SensorTypeNotFoundException
     */
    #[ArrayShape([StandardCardViewReadingResponseDTO::class])]
    public function formatSensorTypeObjectsByReadingType(SensorTypeInterface $cardDTOData): array
    {
        if ($cardDTOData instanceof TemperatureReadingTypeInterface) {
            $sensorData[] = $this->setStandardSensorData($cardDTOData->getTemperature(), Temperature::getReadingTypeName(), Temperature::READING_SYMBOL);
        }
        if ($cardDTOData instanceof HumidityReadingTypeInterface) {
            $sensorData[] = $this->setStandardSensorData($cardDTOData->getHumidObject(), Humidity::getReadingTypeName(), Humidity::READING_SYMBOL);
        }
        if ($cardDTOData instanceof LatitudeReadingTypeInterface) {
            $sensorData[] = $this->setStandardSensorData($cardDTOData->getLatitudeObject(), Latitude::getReadingTypeName());
        }
        if ($cardDTOData instanceof AnalogReadingTypeInterface) {
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
    ): StandardSensorTypeBoundaryViewFormDTO {
        return new StandardSensorTypeBoundaryViewFormDTO(
            $type,
            $sensorTypeObject->getHighReading(),
            $sensorTypeObject->getLowReading(),
            $sensorTypeObject->getConstRecord(),
            $symbol
        );
    }
}
