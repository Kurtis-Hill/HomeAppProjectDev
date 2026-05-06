<?php

namespace App\Services\Sensor\Trigger\TriggerChecker;

use App\Entity\Sensor\SensorTrigger;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Common\OperatorConvertionException;
use App\Repository\Sensor\SensorTriggerRepository;
use App\Services\Common\OperatorValueCheckerConvertor;
use JetBrains\PhpStorm\ArrayShape;

readonly class SensorReadingTriggerChecker implements SensorReadingTriggerCheckerInterface
{
    public function __construct(
        private SensorTriggerRepository $sensorTriggerRepository,
    ) {}

    /**
     * @return SensorTrigger[]
     *@throws OperatorConvertionException
     *
     */
    #[ArrayShape([SensorTrigger::class])]
    public function checkSensorForTriggers(AllSensorReadingTypeInterface $readingType, ?string $day = null, ?string $time = null): array
    {
        $allSensorTriggers = $this->sensorTriggerRepository->findAllSensorTriggersForDayAndTimeForSensorThatTriggers(
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
