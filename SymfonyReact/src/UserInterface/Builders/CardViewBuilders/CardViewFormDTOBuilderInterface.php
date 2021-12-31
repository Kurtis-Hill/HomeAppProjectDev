<?php

namespace App\UserInterface\Builders\CardViewBuilders;

use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\UserInterface\DTO\CardViewDTO\CardViewSensorFormInterface;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;

interface CardViewFormDTOBuilderInterface
{
    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function makeFormDTO(SensorTypeInterface $sensorTypeObject, CardView $cardViewObject, array $usersCardSelections): CardViewSensorFormInterface;
}
