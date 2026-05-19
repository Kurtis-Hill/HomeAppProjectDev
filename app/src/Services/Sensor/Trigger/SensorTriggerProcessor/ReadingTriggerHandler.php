<?php

namespace App\Services\Sensor\Trigger\SensorTriggerProcessor;

use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Common\OperatorConvertionException;
use App\Exceptions\Sensor\BaseReadingTypeNotFoundException;
use App\Exceptions\Sensor\TriggerTypeNotRecognisedException;
use App\Factories\Sensor\TriggerFactories\TriggerTypeHandlerFactory;
use App\Services\Sensor\Trigger\TriggerChecker\SensorReadingTriggerCheckerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class ReadingTriggerHandler implements ReadingTriggerHandlerInterface
{
    public function __construct(
        private SensorReadingTriggerCheckerInterface $sensorReadingTriggerChecker,
        private TriggerTypeHandlerFactory $triggerTypeHandlerFactory,
        private LoggerInterface $elasticLogger,
    ) {}

    public function handleTrigger(AllSensorReadingTypeInterface $readingType): void
    {
        try {
            $triggeredTriggers = $this->sensorReadingTriggerChecker->checkSensorForTriggers($readingType);
        } catch (OperatorConvertionException $e) {
            $this->elasticLogger->error(
                sprintf(
                    'Operator conversion error while checking triggers for base reading type ID %d: %s',
                    $readingType->getBaseReadingType()->getBaseReadingTypeID(),
                    $e->getMessage()
                )
            );
            return;
        }

        if (empty($triggeredTriggers)) {
            return;
        }

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
            } catch (BaseReadingTypeNotFoundException) {
                $this->elasticLogger->error(
                    sprintf(
                        'Base reading type needs to be set for a relay to be activated for trigger %d',
                        $trigger->getSensorTriggerID()
                    )
                );
                continue;
            } catch (Throwable $e) {
                $this->elasticLogger->error(
                    sprintf(
                        'Unexpected error processing trigger %d (%s): %s',
                        $trigger->getSensorTriggerID(),
                        $triggerTypeName,
                        $e->getMessage()
                    )
                );
                continue;
            }
        }
    }
}
