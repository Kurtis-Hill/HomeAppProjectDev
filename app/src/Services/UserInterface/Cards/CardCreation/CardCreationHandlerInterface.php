<?php

namespace App\Services\UserInterface\Cards\CardCreation;

use App\DTOs\UserInterface\Internal\NewCard\NewCardOptionsDTO;
use App\Entity\Sensor\Sensor;
use App\Entity\User\User;

interface CardCreationHandlerInterface
{
    public function createUserCardForSensor(Sensor $sensorObject, User $user, ?NewCardOptionsDTO $cardOptionsDTO = null): array;
}
