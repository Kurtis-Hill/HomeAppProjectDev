<?php

namespace App\Services\Sensor\Trigger\TriggerChecker;

use App\Entity\Sensor\SensorTrigger;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Common\OperatorConvertionException;
use App\Repository\Sensor\SensorTriggerRepository;
use App\Services\Common\OperatorValueCheckerConvertor;
use DateTimeImmutable;
use JetBrains\PhpStorm\ArrayShape;
use Predis\Client;
use Predis\Transaction\MultiExec;
use Psr\Log\LoggerInterface;

readonly class SensorReadingTriggerChecker implements SensorReadingTriggerCheckerInterface
{
    private const SECONDS_BEFORE_DOING_ACTION_AGAIN = 300;

    private const REDIS_KEY = 'triggered_%d_%s';

    public function __construct(
        private SensorTriggerRepository $sensorTriggerRepository,
        private Client $redisClient,
        private string $env,
        private LoggerInterface $elasticLogger,
    ) {}

    /**
     * @return SensorTrigger[]
     *@throws OperatorConvertionException
     *
     */
    #[ArrayShape([SensorTrigger::class])]
    public function checkSensorForTriggers(
        AllSensorReadingTypeInterface $readingType,
        ?string $day = null,
        ?string $time = null
    ): array {
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
                if ($this->env !== 'test') {
                    $redisKey = sprintf(self::REDIS_KEY, $sensorTrigger->getBaseReadingTypeToTriggers()->getBaseReadingTypeID(), $operator->getOperatorSymbol());
                    if ($this->redisClient->exists($redisKey)) {
                        $this->elasticLogger->error(
                            sprintf(
                                'Trigger %d for base reading type ID %d has been triggered recently and will not be processed again yet.',
                                $sensorTrigger->getSensorTriggerID(),
                                $sensorTrigger->getBaseReadingTypeToTriggers()->getBaseReadingTypeID()
                            ),
                            [
                                'sensorID' => $sensorTrigger->getBaseReadingTypeToTriggers()->getSensor()->getSensorID(),
                                'baseReadingTypeID' => $sensorTrigger->getBaseReadingTypeToTriggers()->getBaseReadingTypeID(),
                                'operator' => $operator->getOperatorSymbol(),
                            ]
                        );
                        continue;
                    }

                    $seconds = self::SECONDS_BEFORE_DOING_ACTION_AGAIN;
                    $this->redisClient->transaction(function (MultiExec $tx) use ($redisKey, $seconds) {
                        $tx->set($redisKey, true);
                        $tx->expire($redisKey,  $seconds);
                    });
                }

                $lastUpdate = $sensorTrigger->getBaseReadingTypeToTriggers()->getUpdatedAt()->getTimestamp();
                $sensorReadingInterval = $sensorTrigger->getBaseReadingTypeToTriggers()->getSensor()->getReadingInterval();

                $whenNextUpdateShouldBe = $lastUpdate + $sensorReadingInterval;
                $now = (new DateTimeImmutable())->getTimestamp();

                if ($whenNextUpdateShouldBe < $now) {
                    $this->elasticLogger->info("Sensor not active ignoring trigger", [
                        'sensorTriggerID' => $sensorTrigger->getSensorTriggerID(),
                        'baseReadingType' => $sensorTrigger->getBaseReadingTypeToTriggers()->getBaseReadingTypeID(),
                        'lastUpdate' => $lastUpdate,
                        'sensorReadingInterval' => $sensorReadingInterval,
                        'whenNextUpdateShouldBe' => $whenNextUpdateShouldBe,
                        'now' => $now
                    ]);
                    continue;
                }

                $triggeredTriggers[] = $sensorTrigger;
            }
        }

        return $triggeredTriggers;
    }
}
