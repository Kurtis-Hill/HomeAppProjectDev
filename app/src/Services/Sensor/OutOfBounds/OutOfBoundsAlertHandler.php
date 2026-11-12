<?php

namespace App\Services\Sensor\OutOfBounds;

use App\Entity\Sensor\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
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

        $readingTypeName = $standardReadingSensor::getReadingTypeName();

        $message = sprintf(
            "%s *Out of bounds alert: %s %s*\n"
            . ">%s *Sensor:* %s\n"
            . ">🏠 *Room:* %s\n"
            . ">📊 *Current reading:* `%s`\n"
            . ">🚧 *%s limit:* `%s`",
            $isHighReading ? '🔺' : '🔻',
            ucfirst($readingTypeName),
            $isHighReading ? 'too high' : 'too low',
            $this->getReadingTypeEmoji($readingTypeName, $isHighReading),
            $standardReadingSensor->getSensor()->getSensorName(),
            $standardReadingSensor->getSensor()->getDevice()->getRoomObject()->getRoom(),
            $currentReading,
            $isHighReading ? 'High' : 'Low',
            $isHighReading ? $highReading : $lowReading,
        );

        if ($this->env !== 'test') {
            $seconds = $standardReadingSensor->getOutOfBoundsAlertTimer() ?? 60 * 60;
            $this->redisClient->transaction(function (MultiExec $tx) use ($redisKey, $seconds) {
                $tx->set($redisKey, true);
                $tx->expire($redisKey,  $seconds);
            });

            $this->homeAppAlertClient->sendAlert($message);
        }
    }

    private function getReadingTypeEmoji(string $readingTypeName, bool $isHighReading): string
    {
        return match ($readingTypeName) {
            Temperature::READING_TYPE => $isHighReading ? '🔥' : '🥶',
            Humidity::READING_TYPE => '💧',
            Analog::READING_TYPE => '🎛️',
            Latitude::READING_TYPE => '🧭',
            default => '📡',
        };
    }
}
