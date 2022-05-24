<?php

namespace App\UserInterface\Builders\CardViewBuilders;

use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\UserInterface\DTO\Response\CardForms\CardViewSensorFormInterface;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;

interface CardViewFormDTOBuilderInterface
{
    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function buildFormDTO(SensorTypeInterface $sensorTypeObject, CardView $cardViewObject, array $usersCardSelections): CardViewSensorFormInterface;
}
