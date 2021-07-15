<?php

namespace App\DTOs\Factorys\CardDTOs;

use App\DTOs\Sensors\CardDTOs\CardViewSensorFormDTO;
use App\HomeAppSensorCore\Interfaces\SensorInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use JetBrains\PhpStorm\ArrayShape;

class CardViewFormDTOFactory extends AbstractCardDataFactory implements CardFormDTOInterface
{
    /**
     * @param SensorInterface $sensorData
     * @param array $usersCardSelections
     * @return CardViewSensorFormDTO
     */
    public function makeDTO(SensorInterface $sensorData, array $usersCardSelections): CardViewSensorFormDTO
    {
        $formattedSensorData = $this->filterSensorTypesAndGetData($sensorData);
        $usersCurrentCardDisplaySettings = $this->setUsersCurrentCardViewData($sensorData);

        return new CardViewSensorFormDTO(
            $usersCurrentCardDisplaySettings,
            $usersCardSelections,
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
