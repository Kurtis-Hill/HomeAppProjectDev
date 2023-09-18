<?php

namespace App\UserInterface\Builders\CardViewDTOBuilders\FormResponse;

use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\UserInterface\Builders\CardStateDTOBuilders\CardStateDTOBuilder;
use App\UserInterface\Builders\CardViewDTOBuilders\CurrentReadingResponse\AbstractSensorTypeViewDTOBuilder;
use App\UserInterface\Builders\ColoursDTOBuilders\ColourDTOBuilder;
use App\UserInterface\Builders\IconDTOBuilder\IconDTOBuilder;
use App\UserInterface\DTO\Response\CardForms\CardViewSensorFormInterface;
use App\UserInterface\DTO\Response\CardForms\StandardCardViewSensorFormResponseDTO;
use App\UserInterface\DTO\Response\CardView\CardUserSelectionEncapsulationDTO;
use App\UserInterface\Entity\Card\CardView;

class StandardCardViewFormDTOBuilder extends AbstractSensorTypeViewDTOBuilder implements CardViewFormDTOBuilderInterface
{
    public function buildFormDTO(
        SensorTypeInterface $sensorTypeObject,
        CardView $cardViewObject,
        CardUserSelectionEncapsulationDTO $usersCardSelectionOptions
    ): CardViewSensorFormInterface {
        $cardBuilder = $this->sensorTypeDTOBuilderFactory->getSensorDataDTOBuilderService($sensorTypeObject->getReadingTypeName());
        $formattedSensorTypeObjects = $cardBuilder->formatSensorTypeObjectsByReadingType($sensorTypeObject);

        return new StandardCardViewSensorFormResponseDTO(
            $sensorTypeObject->getSensor()->getSensorID(),
            IconDTOBuilder::buildIconResponseDTO($cardViewObject->getCardIconID()),
            ColourDTOBuilder::buildColourResponseDTO($cardViewObject->getCardColourID()),
            CardStateDTOBuilder::buildCardStateResponseDTO($cardViewObject->getCardStateID()),
            $cardViewObject->getCardViewID(),
            $usersCardSelectionOptions,
            $formattedSensorTypeObjects
        );
    }
}
