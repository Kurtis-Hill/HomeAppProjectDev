<?php

namespace App\DTOs\CardDTOs\Builders\SensorTypeBuilders;

use App\DTOs\CardDTOs\Builders\CardBuilderDTOInterface;
use App\DTOs\CardDTOs\Sensors\DTOs\AllCardViewDTOInterface;
use App\DTOs\CardDTOs\Sensors\DTOs\CardViewFormDTO;
use App\DTOs\CardDTOs\Sensors\DTOs\CardViewSensorFormDTO;
use App\HomeAppSensorCore\Interfaces\SensorInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use JetBrains\PhpStorm\ArrayShape;

class SensorTypeCardViewFormDTOBuilder extends AbstractSensorTypeCardDataBuilder implements CardBuilderDTOInterface
{
    /**
     * @param SensorInterface $sensorData
     * @param array $extraSensorData
     * @return CardViewSensorFormDTO
     */
    public function makeDTO(SensorInterface $sensorData, array $extraSensorData = []): CardViewFormDTO
    {
        $formattedSensorData = $this->filterSensorTypesAndGetData($sensorData);
        $usersCurrentCardDisplaySettings = $this->setUsersCurrentCardViewData($sensorData);
        $usersCardSelections = $extraSensorData;


        return new CardViewSensorFormDTO(
            $usersCurrentCardDisplaySettings['cardIcon'],
            $usersCurrentCardDisplaySettings['colours'],
            $usersCurrentCardDisplaySettings['states'],
            $usersCurrentCardDisplaySettings['cardViewID'],
            $usersCardSelections['icons'],
            $usersCardSelections['colours'],
            $usersCardSelections['states'],
            $formattedSensorData
        );
    }

    /**
     * @param SensorInterface $cardDTOData
     * @return array
     */
    protected function setUsersCurrentCardViewData(SensorInterface $cardDTOData): array
    {
        if ($cardDTOData->getCardViewObject() === null) {
            throw new \RuntimeException('Card View Object Has Not Been Set For The Form DTO');
        }

        $cardData['cardIcon'] = [
            'iconID' => $cardDTOData->getCardViewObject()->getCardIconID()->getIconID(),
            'iconName'=> $cardDTOData->getCardViewObject()->getCardIconID()->getIconName()
        ];

        $cardData['colours'] = [
            'colourID' => $cardDTOData->getCardViewObject()->getCardColourID()->getColourID(),
            'colour' => $cardDTOData->getCardViewObject()->getCardColourID()->getColour(),
        ];

        $cardData['states'] = [
            'cardStateID' => $cardDTOData->getCardViewObject()->getCardStateID()->getCardstateID(),
            'state' => $cardDTOData->getCardViewObject()->getCardStateID()->getState(),
        ];

        $cardData['cardViewID'] = $cardDTOData->getCardViewObject()->getCardViewID();

        return $cardData;
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
            'highReading' => "float|int|string",
            'lowReading' => "float|int|string",
            'readingSymbol' => "null|string",
            'constRecord' => "bool"
        ])
    ]
    protected function setStandardSensorData(
        StandardReadingSensorInterface $sensorTypeObject,
        string $type,
        string $symbol = null
    ): array
    {
        return [
            'sensorType' => $type,
            'highReading' => is_float($sensorTypeObject->getHighReading())
                ? number_format($sensorTypeObject->getHighReading(), 2)
                : $sensorTypeObject->getHighReading(),
            'lowReading' => is_float($sensorTypeObject->getLowReading())
                ? number_format($sensorTypeObject->getLowReading(), 2)
                : $sensorTypeObject->getLowReading(),
            'readingSymbol' => $symbol,
            'constRecord' => $sensorTypeObject->getConstRecord(),
        ];
    }
}
