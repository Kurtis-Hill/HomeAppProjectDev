<?php

namespace App\Sensors\SensorServices\TriggerChecker;

use App\Common\Services\OperatorValueCheckerConvertor;
use App\Sensors\Entity\SensorTrigger;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Repository\SensorTriggerRepository;
use JetBrains\PhpStorm\ArrayShape;

readonly class SensorReadingTriggerChecker implements SensorReadingTriggerCheckerInterface
{
    public function __construct(
        private SensorTriggerRepository $sensorTriggerRepository,
    ) {}

    #[ArrayShape([SensorTrigger::class])]
    public function checkSensorForTriggers(AllSensorReadingTypeInterface $readingType): array
    {
        $allSensorTriggers = $this->sensorTriggerRepository->findAllSensorTriggersForDayAndTime($readingType->getSensor());

        $triggeredTriggers = [];
        foreach ($allSensorTriggers as $sensorTrigger) {
            $operator = $sensorTrigger->getOperator();
            $valueThatTriggers = $sensorTrigger->getValueThatTriggers();

            $currentValue = $readingType->getCurrentReading();

            $hasNewReadingTriggered = OperatorValueCheckerConvertor::checkValuesAgainstOperator(
                $operator,
                $currentValue,
                $valueThatTriggers
            );

            if ($hasNewReadingTriggered === true) {
                $triggeredTriggers[] = $sensorTrigger;
            }
        }

        return $triggeredTriggers;
    }
}
