<?php

namespace App\Builders\UserInterface\CardViewDTOBuilders\FormResponse;

use App\DTOs\UserInterface\Response\CardForms\CardViewSensorFormInterface;
use App\DTOs\UserInterface\Response\CardView\CardUserSelectionEncapsulationDTO;
use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use App\Entity\UserInterface\Card\CardView;
use App\Exceptions\UserInterface\SensorTypeBuilderFailureException;

interface CardViewFormDTOBuilderInterface
{
    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function buildFormDTO(SensorTypeInterface $sensorTypeObject, CardView $cardViewObject, CardUserSelectionEncapsulationDTO $usersCardSelectionOptions): CardViewSensorFormInterface;
}
