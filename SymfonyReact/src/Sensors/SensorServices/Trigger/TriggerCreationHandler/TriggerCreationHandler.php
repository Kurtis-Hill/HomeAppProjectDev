<?php

namespace App\Sensors\SensorServices\Trigger\TriggerCreationHandler;

use App\Sensors\DTO\Internal\Trigger\CreateNewTriggerDTO;

class TriggerCreationHandler implements TriggerCreationHandlerInterface
{
    public function createTrigger(CreateNewTriggerDTO $createNewTriggerDTO): array
    {
        $trigger = $createNewTriggerDTO->getTrigger();
        $triggerType = $trigger->getTriggerType();
        $operator = $trigger->getOperator();
        $baseReadingTypeThatTriggers = $trigger->getBaseReadingTypeThatTriggers();
        $baseReadingTypeThatIsTriggered = $trigger->getBaseReadingTypeThatIsTriggered();

        $triggerTypeName = $triggerType->getTriggerTypeName();
        $operatorName = $operator->getOperatorName();
        $baseReadingTypeThatTriggersName = $baseReadingTypeThatTriggers !== null ? $baseReadingTypeThatTriggers->getReadingTypeName() : null;
        $baseReadingTypeThatIsTriggeredName = $baseReadingTypeThatIsTriggered !== null ? $baseReadingTypeThatIsTriggered->getReadingTypeName() : null;

        return [
            'triggerTypeName' => $triggerTypeName,
            'operatorName' => $operatorName,
            'baseReadingTypeThatTriggersName' => $baseReadingTypeThatTriggersName,
            'baseReadingTypeThatIsTriggeredName' => $baseReadingTypeThatIsTriggeredName,
        ];
    }
}
