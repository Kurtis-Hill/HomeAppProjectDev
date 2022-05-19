<?php

namespace App\UserInterface\Builders\CardViewBuilders;

use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\UserInterface\DTO\CardViewDTO\CardViewSensorFormInterface;
use App\UserInterface\DTO\CardViewDTO\StandardCardViewSensorFormDTO;
use App\UserInterface\Entity\Card\CardView;
use JetBrains\PhpStorm\ArrayShape;

class StandardCardViewFormDTOBuilder extends AbstractSensorTypeViewDTOBuilder implements CardViewFormDTOBuilderInterface
{
    public function makeFormDTO(SensorTypeInterface $sensorTypeObject, CardView $cardViewObject, array $usersCardSelections): CardViewSensorFormInterface
    {
        $cardBuilder = $this->sensorTypeDTOBuilderFactory->getSensorDataDTOBuilderService($sensorTypeObject->getSensorTypeName());
        $formattedSensorTypeObjects = $cardBuilder->formatSensorTypeObjects($sensorTypeObject);

        $usersCurrentCardDisplaySettings = $this->setUsersCurrentCardViewData($cardViewObject);

        return new StandardCardViewSensorFormDTO(
            $sensorTypeObject->getSensorObject()->getSensorNameID(),
            $usersCurrentCardDisplaySettings['cardIcon'],
            $usersCurrentCardDisplaySettings['colours'],
            $usersCurrentCardDisplaySettings['states'],
            $usersCurrentCardDisplaySettings['cardViewID'],
            $usersCardSelections['icons'],
            $usersCardSelections['colours'],
            $usersCardSelections['states'],
            $formattedSensorTypeObjects
        );
    }

    #[ArrayShape([
        'cardIcon' => [
            'iconID' => "int",
            'iconName' => "string",
        ],
        'colours' => [
            'colourID' => "int",
            'colour' => "int",
        ],
        'states' => [
            'cardStateID' => "int",
            'state' =>  'string',
        ],
        'cardViewID' => 'int'
    ])]
    private function setUsersCurrentCardViewData(CardView $cardView): array
    {
        $cardData['cardIcon'] = [
            'iconID' => $cardView->getCardIconID()->getIconID(),
            'iconName'=> $cardView->getCardIconID()->getIconName()
        ];

        $cardData['colours'] = [
            'colourID' => $cardView->getCardColourID()->getColourID(),
            'colour' => $cardView->getCardColourID()->getColour(),
        ];

        $cardData['states'] = [
            'cardStateID' => $cardView->getCardStateID()->getCardstateID(),
            'state' => $cardView->getCardStateID()->getState(),
        ];

        $cardData['cardViewID'] = $cardView->getCardViewID();

        return $cardData;
    }
}
