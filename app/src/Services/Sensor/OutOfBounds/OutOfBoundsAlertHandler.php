<?php

namespace App\Services\Sensor\OutOfBounds;

use App\Entity\Sensor\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Services\Common\Client\HomeAppAlertClientInterface;
use Predis\Client;
use Predis\Transaction\MultiExec;

readonly class OutOfBoundsAlertHandler
{
    private const REDIS_KEY = 'out_of_bounds_alert_%s';

    public function __construct(
        private HomeAppAlertClientInterface $homeAppAlertClient,
        private Client $redisClient,
        private string $env,
    ) {
    }

    public function handlerAlert(
        StandardReadingSensorInterface $standardReadingSensor,
        OutOfBoundsEntityInterface $outOfBoundsEntity,
    ): void {
        $redisKey = sprintf(self::REDIS_KEY, $outOfBoundsEntity->getBaseSensorReadingType()->getBaseReadingTypeID());
        if ($this->redisClient->exists($redisKey)) {
            return;
        }

        $currentReading = $outOfBoundsEntity->getSensorReading();

        $highReading = $standardReadingSensor->getHighReading();
        $lowReading = $standardReadingSensor->getLowReading();

        $isHighReading = $currentReading > $highReading;

        $message = sprintf(
            'Sensor reading: %s is out of bounds for  sensor: %s in: %s  when the %s limit was set to %s',
            $currentReading,
            $standardReadingSensor->getSensor()->getSensorName(),
            $standardReadingSensor->getSensor()->getDevice()->getRoomObject()->getRoom(),
            $isHighReading ? 'high' : 'low',
            $isHighReading ? $highReading : $lowReading,
        );

        if ($this->env !== 'test') {
            $this->redisClient->transaction(function (MultiExec $tx) use ($redisKey) {
                $tx->set($redisKey, true);
                $tx->expire($redisKey, 60 * 60);
            });
        }

        $this->homeAppAlertClient->sendAlert($message);
    }
}
