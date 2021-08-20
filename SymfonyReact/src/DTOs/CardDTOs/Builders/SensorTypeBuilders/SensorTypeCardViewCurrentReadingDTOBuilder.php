<?php


namespace App\DTOs\CardDTOs\Builders\SensorTypeBuilders;

use App\DTOs\CardDTOs\Builders\CardBuilderDTOInterface;
use App\DTOs\CardDTOs\Sensors\DTOs\AllCardViewDTOInterface;
use App\DTOs\CardDTOs\Sensors\DTOs\CardDTOInterface;
use App\DTOs\CardDTOs\Sensors\DTOs\CurrentReadingCardDataDTO;
use App\HomeAppSensorCore\Interfaces\SensorInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;

class SensorTypeCardViewCurrentReadingDTOBuilder extends AbstractSensorTypeCardDataBuilder implements CardBuilderDTOInterface
{
    /**
     * @param SensorInterface $sensorData
     * @param array $extraSensorData
     * @return CardDTOInterface
     */
    public function makeDTO(SensorInterface $sensorData, array $extraSensorData = []): CardDTOInterface
    {
        $formattedSensorData = $this->filterSensorTypesAndGetData($sensorData);

        $sensor = $sensorData->getSensorObject();
        $cardViewData = $sensorData->getCardViewObject();

        if (!$cardViewData) {
            throw new RuntimeException('No Card Data Set, Card DTO Creation Failed');
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

    /**
     * @param StandardReadingSensorInterface $sensorTypeObject
     * @param string $type
     * @param string|null $symbol
     * @return array
     */
    #[ArrayShape(
        [
            'sensorType' => "string",
            'highReading' => "float|int",
            'lowReading' => "float|int",
            'currentReading' => "float|int",
            'getCurrentHighDifference' => "float|int",
            'getCurrentLowDifference' => "float|int",
            'readingSymbol' => "null|string",
            'time' => "string"
        ]
    )
    ]
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
