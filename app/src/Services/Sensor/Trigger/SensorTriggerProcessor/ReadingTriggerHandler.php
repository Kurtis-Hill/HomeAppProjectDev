<?php

namespace App\Services\Sensor\Trigger\SensorTriggerProcessor;

use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Common\OperatorConvertionException;
use App\Exceptions\Sensor\BaseReadingTypeNotFoundException;
use App\Exceptions\Sensor\TriggerTypeNotRecognisedException;
use App\Factories\Sensor\TriggerFactories\TriggerTypeHandlerFactory;
use App\Services\Sensor\Trigger\TriggerChecker\SensorReadingTriggerCheckerInterface;
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
                } catch (BaseReadingTypeNotFoundException) {
                    $this->elasticLogger->error(sprintf('Base reading type needs to be set for a relay to be activated for trigger %d', $trigger->getSensorTriggerID()));
                    continue;
                } catch (Exception $e) {
                    $this->elasticLogger->error(sprintf('Failed to process trigger %d', $trigger->getSensorTriggerID()),[$e->getMessage()]);
                    continue;
                }
            }
        }
    }
}
