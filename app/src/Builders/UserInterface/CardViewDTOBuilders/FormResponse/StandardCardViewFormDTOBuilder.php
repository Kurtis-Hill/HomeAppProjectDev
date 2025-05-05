<?php

namespace App\Builders\UserInterface\CardViewDTOBuilders\FormResponse;

use App\Builders\UserInterface\CardStateDTOBuilders\CardStateDTOBuilder;
use App\Builders\UserInterface\CardViewDTOBuilders\CurrentReadingResponse\AbstractSensorTypeViewDTOBuilder;
use App\Builders\UserInterface\ColoursDTOBuilders\ColourDTOBuilder;
use App\Builders\UserInterface\IconDTOBuilder\IconDTOBuilder;
use App\DTOs\UserInterface\Response\CardForms\CardViewSensorFormInterface;
use App\DTOs\UserInterface\Response\CardForms\StandardCardViewSensorFormResponseDTO;
use App\DTOs\UserInterface\Response\CardView\CardUserSelectionEncapsulationDTO;
use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use App\Entity\UserInterface\Card\CardView;

class StandardCardViewFormDTOBuilder extends AbstractSensorTypeViewDTOBuilder implements CardViewFormDTOBuilderInterface
{
    public function buildFormDTO(
        SensorTypeInterface $sensorTypeObject,
        CardView $cardViewObject,
        CardUserSelectionEncapsulationDTO $usersCardSelectionOptions
    ): CardViewSensorFormInterface {
        $cardBuilder = $this->sensorTypeDTOBuilderFactory->getSensorDataDTOBuilderService($sensorTypeObject->getSensorTypeName());
        $formattedSensorTypeObjects = $cardBuilder->formatSensorTypeObjectsByReadingType($sensorTypeObject, $cardViewObject->getSensor());

        return new StandardCardViewSensorFormResponseDTO(
            $cardViewObject->getSensor()->getSensorID(),
            IconDTOBuilder::buildIconResponseDTO($cardViewObject->getCardIconID()),
            ColourDTOBuilder::buildColourResponseDTO($cardViewObject->getCardColourID()),
            CardStateDTOBuilder::buildCardStateResponseDTO($cardViewObject->getCardStateID()),
            $cardViewObject->getCardViewID(),
            $usersCardSelectionOptions,
            $formattedSensorTypeObjects
        );
    }
}
