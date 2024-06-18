<?php

namespace App\Services\Sensor\Trigger\TriggerCreationHandler;

use App\DTOs\Sensor\Internal\Trigger\CreateNewTriggerDTO;

interface TriggerCreationHandlerInterface
{
    public function createTrigger(CreateNewTriggerDTO $createNewTriggerDTO): array;
}
