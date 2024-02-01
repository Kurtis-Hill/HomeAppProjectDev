<?php

namespace App\Sensors\SensorServices\Trigger\SensorTriggerProcessor;

use App\Common\Exceptions\OperatorConvertionException;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\TriggerTypeNotRecognisedException;
use App\Sensors\Factories\TriggerFactories\TriggerTypeHandlerFactory;
use App\Sensors\SensorServices\Trigger\TriggerChecker\SensorReadingTriggerCheckerInterface;
use Exception;
use Psr\Log\LoggerInterface;

readonly class ReadingTriggerHandler implements ReadingTriggerHandlerInterface
{
    public function __construct(
        private SensorReadingTriggerCheckerInterface $sensorReadingTriggerChecker,
        private TriggerTypeHandlerFactory $triggerTypeHandlerFactory,
        private LoggerInterface $elasticLogger,
    ) {}

    /**
     * @throws OperatorConvertionException
     */
    public function handleTrigger(AllSensorReadingTypeInterface $readingType): void
    {
        $triggeredTriggers = $this->sensorReadingTriggerChecker->checkSensorForTriggers($readingType);
        if (!empty($triggeredTriggers)) {
            foreach ($triggeredTriggers as $trigger) {
                $triggerTypeName = $trigger->getTriggerType()->getTriggerTypeName();

                try {
                    $triggerTypeHandler = $this->triggerTypeHandlerFactory->getTriggerTypeHandler($triggerTypeName);
                } catch (TriggerTypeNotRecognisedException $e) {
                    $this->elasticLogger->error($e->getMessage());
                    continue;
                }

                try {
                    $triggerTypeHandler->processTrigger($trigger);
                } catch (Exception $e) {
                    $this->elasticLogger->error(sprintf('Failed to process trigger %d', $trigger->getSensorTriggerID()),[$e->getMessage()]);
                    continue;
                }
            }
        }
    }
}
