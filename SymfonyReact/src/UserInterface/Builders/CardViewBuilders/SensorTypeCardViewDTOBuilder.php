<?php

namespace App\UserInterface\Builders\CardViewBuilders;

use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;

interface SensorTypeCardViewDTOBuilder
{
    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function makeDTO(array $cardData);
}
