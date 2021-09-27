<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\OufOfBounds;

use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Repository\ORM\OutOfBounds\OutOfBoundsHumidityRepository;
use App\ESPDeviceSensor\Repository\ORM\OutOfBounds\OutOfBoundsRepositoryInterface;
use App\ESPDeviceSensor\Repository\ORM\OutOfBounds\OutOfRangeAnalogORMRepository;
use App\ESPDeviceSensor\Repository\ORM\OutOfBounds\OutOfBoundsTempORMRepository;

class ORMOutOfBoundsFactory implements OutOfBoundsFactoryInterface
{
    private OutOfBoundsTempORMRepository $outOfBoundsTemp;

    private OutOfBoundsHumidityRepository $outOfBoundsHumid;

    private OutOfRangeAnalogORMRepository $outOfBoundsAnalog;

    public function __construct(
      OutOfBoundsTempORMRepository $outBoundsTempORMRepository,
      OutOfBoundsHumidityRepository $outOfBoundsHumidORMRepository,
      OutOfRangeAnalogORMRepository $outOfBoundsAnalogORMRepository,
    ) {
        $this->outOfBoundsTemp = $outBoundsTempORMRepository;
        $this->outOfBoundsHumid = $outOfBoundsHumidORMRepository;
        $this->outOfBoundsAnalog = $outOfBoundsAnalogORMRepository;
    }

    public function getOutOfBoundsServiceRepository(string $sensorReadingType): OutOfBoundsRepositoryInterface
    {
        return match ($sensorReadingType) {
            Temperature::class => $this->outOfBoundsTemp,
            Humidity::class => $this->outOfBoundsHumid,
            Analog::class => $this->outOfBoundsAnalog,
        };
    }
}
