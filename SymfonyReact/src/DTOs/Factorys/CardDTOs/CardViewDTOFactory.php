<?php


namespace App\DTOs\Factorys\CardDTOs;

use App\DTOs\Sensors\CurrentReadingCardDataDTO;
use App\HomeAppSensorCore\Interfaces\DTO\CurrentSensorReadingCardInterface;
use App\HomeAppSensorCore\Interfaces\SensorInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;

class CardViewDTOFactory extends AbstractCardDataFactory implements CurrentSensorReadingCardInterface
{
    public function makeDTO(SensorInterface $sensorData): CurrentReadingCardDataDTO
    {
        $formattedSensorData = $this->filterSensorTypesAndGetData($sensorData);

        $sensor = $sensorData->getSensorObject();
        $cardViewData = $sensorData->getCardViewObject();

        if (!$cardViewData) {
            throw new \RuntimeException('No Card Data Set, Card DTO Creation Failed');
        }

        return new CurrentReadingCardDataDTO(
            $sensor->getSensorName(),
            $sensor->getSensorTypeID()->getSensorType(),
            $sensor->getDeviceNameID()->getRoomObject()->getRoom(),
            $cardViewData->getCardIconID()->getIconName(),
            $cardViewData->getCardColourID()->getColour(),
            $cardViewData->getCardViewID(),
            $formattedSensorData,
        );
    }

    protected function setStandardSensorData(StandardReadingSensorInterface $sensorTypeObject, string $type, string $symbol = null): array
    {
        return [
            'sensorType' => $type,
            'highReading' => is_float($sensorTypeObject->getHighReading())
                ? number_format($sensorTypeObject->getHighReading(), 2)
                : $sensorTypeObject->getHighReading(),
            'lowReading' => is_float($sensorTypeObject->getLowReading())
                ? number_format($sensorTypeObject->getLowReading(), 2)
                : $sensorTypeObject->getLowReading(),
            'currentReading' => is_float($sensorTypeObject->getCurrentReading())
                ?  number_format($sensorTypeObject->getCurrentReading(), 2)
                : $sensorTypeObject->getCurrentReading(),
            'getCurrentHighDifference' => is_float($sensorTypeObject->getMeasurementDifferenceHighReading())
                ? number_format($sensorTypeObject->getMeasurementDifferenceHighReading(), 2)
                : $sensorTypeObject->getMeasurementDifferenceHighReading(),
            'getCurrentLowDifference' => is_float($sensorTypeObject->getMeasurementDifferenceLowReading())
                ? number_format($sensorTypeObject->getMeasurementDifferenceLowReading(), 2)
                : $sensorTypeObject->getMeasurementDifferenceLowReading(),
            'readingSymbol' => $symbol,
            'time' => $sensorTypeObject->getTime()->format('d-m H:i:s')
        ];
    }
}
