<?php

namespace App\Sensors\SensorServices\Trigger\TriggerChecker;

use App\Common\Exceptions\OperatorConvertionException;
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

    /**
     * @throws OperatorConvertionException
     *
     * @return SensorTrigger[]
     */
    #[ArrayShape([SensorTrigger::class])]
    public function checkSensorForTriggers(AllSensorReadingTypeInterface $readingType, ?string $day = null, ?string $time = null): array
    {
        $allSensorTriggers = $this->sensorTriggerRepository->findAllSensorTriggersForDayAndTime(
            $readingType,
            $day,
            $time
        );
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
