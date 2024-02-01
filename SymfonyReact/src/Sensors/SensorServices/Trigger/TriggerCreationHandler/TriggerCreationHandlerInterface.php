<?php

namespace App\Sensors\SensorServices\Trigger\TriggerCreationHandler;

use App\Sensors\DTO\Internal\Trigger\CreateNewTriggerDTO;

interface TriggerCreationHandlerInterface
{
    public function createTrigger(CreateNewTriggerDTO $createNewTriggerDTO): array;
}
