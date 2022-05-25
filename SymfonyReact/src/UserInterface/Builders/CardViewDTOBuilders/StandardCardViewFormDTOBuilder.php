<?php

namespace App\UserInterface\Builders\CardViewDTOBuilders;

use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\UserInterface\Builders\CardStateDTOBuilders\CardStateDTOBuilder;
use App\UserInterface\Builders\ColoursDTOBuilders\ColourDTOBuilder;
use App\UserInterface\Builders\IconDTOBuilder\IconDTOBuilder;
use App\UserInterface\DTO\Response\CardForms\CardViewSensorFormInterface;
use App\UserInterface\DTO\Response\CardForms\StandardCardViewSensorFormResponseDTO;
use App\UserInterface\Entity\Card\CardView;

class StandardCardViewFormDTOBuilder extends AbstractSensorTypeViewDTOBuilder implements CardViewFormDTOBuilderInterface
{
    public function buildFormDTO(
        SensorTypeInterface $sensorTypeObject,
        CardView $cardViewObject,
        array $usersCardSelections
    ): CardViewSensorFormInterface {
        $cardBuilder = $this->sensorTypeDTOBuilderFactory->getSensorDataDTOBuilderService($sensorTypeObject->getSensorTypeName());
        $formattedSensorTypeObjects = $cardBuilder->formatSensorTypeObjectsByReadingType($sensorTypeObject);

        return new StandardCardViewSensorFormResponseDTO(
            $sensorTypeObject->getSensorObject()->getSensorNameID(),
            IconDTOBuilder::buildIconResponseDTO($cardViewObject->getCardIconID()),
            ColourDTOBuilder::buildColourResponseDTO($cardViewObject->getCardColourID()),
            CardStateDTOBuilder::buildCardStateResponseDTO($cardViewObject->getCardStateID()),
            $cardViewObject->getCardViewID(),
            $usersCardSelections['icons'] ?? [],
            $usersCardSelections['colours'] ?? [],
            $usersCardSelections['states'] ?? [],
            $formattedSensorTypeObjects
        );
    }
}
