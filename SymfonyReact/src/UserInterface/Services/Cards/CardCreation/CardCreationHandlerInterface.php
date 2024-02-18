<?php

namespace App\UserInterface\Services\Cards\CardCreation;

use App\Sensors\Entity\Sensor;
use App\User\Entity\User;
use App\UserInterface\DTO\Internal\NewCard\NewCardOptionsDTO;

interface CardCreationHandlerInterface
{
    public function createUserCardForSensor(Sensor $sensorObject, User $user, ?NewCardOptionsDTO $cardOptionsDTO = null): array;
}
