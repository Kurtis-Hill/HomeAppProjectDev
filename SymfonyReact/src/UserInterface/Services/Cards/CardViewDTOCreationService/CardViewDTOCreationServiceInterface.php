<?php

namespace App\UserInterface\Services\Cards\CardViewDTOCreationService;

use App\UserInterface\Exceptions\CardTypeNotRecognisedException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;

interface CardViewDTOCreationServiceInterface
{
    /**
     * @throws CardTypeNotRecognisedException
     * @throws SensorTypeBuilderFailureException
     */
    public function buildCurrentReadingSensorCards(array $sensorData);
}
