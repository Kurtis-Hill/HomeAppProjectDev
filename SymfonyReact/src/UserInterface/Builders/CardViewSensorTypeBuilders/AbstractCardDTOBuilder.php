<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Sensors\Exceptions\SensorTypeNotFoundException;
use App\UserInterface\DTO\Response\CardForms\Boundary\BoolSensorTypeBoundaryViewFormDTO;
use App\UserInterface\DTO\Response\CardForms\Boundary\StandardSensorTypeBoundaryViewFormDTO;
use App\UserInterface\DTO\Response\CardViewReadingDTO\CardViewReadingResponseDTOInterface;
use JetBrains\PhpStorm\ArrayShape;

abstract class AbstractCardDTOBuilder
{
    /**
     * @throws SensorTypeNotFoundException
     */
    #[ArrayShape([CardViewReadingResponseDTOInterface::class])]
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
        if ($cardDTOData instanceof RelayReadingTypeInterface) {
            $sensorData[] = $this->setBoolSensorData($cardDTOData->getRelay(), Relay::READING_TYPE);
        }
        if ($cardDTOData instanceof MotionSensorReadingTypeInterface) {
            $sensorData[] = $this->setBoolSensorData($cardDTOData->getMotion(), Motion::READING_TYPE);
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

    private function setBoolSensorData(
        BoolReadingSensorInterface $sensorTyeObject,
        string $type,
        string $symbol = null,
    ): BoolSensorTypeBoundaryViewFormDTO {
        $b = new BoolSensorTypeBoundaryViewFormDTO(
            $type,
//            $sensorTyeObject->getCurrentReading(),
            $sensorTyeObject->getExpectedReading(),
//            $sensorTyeObject->getRequestedReading(),
            $sensorTyeObject->getConstRecord(),
            $symbol
        );
//dd($b);
return $b;
    }
}
