<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\OufOfBounds;

use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotSupportedException;
use App\ESPDeviceSensor\Repository\ORM\OutOfBounds\OutOfBoundsHumidityRepository;
use App\ESPDeviceSensor\Repository\ORM\OutOfBounds\OutOfBoundsRepositoryInterface;
use App\ESPDeviceSensor\Repository\ORM\OutOfBounds\OutOfBoundsAnalogRepository;
use App\ESPDeviceSensor\Repository\ORM\OutOfBounds\OutOfBoundsTempORMRepository;

class OutOfBoundsORMFactory implements OutOfBoundsORMFactoryInterface
{
    private OutOfBoundsTempORMRepository $outOfBoundsTemp;

    private OutOfBoundsHumidityRepository $outOfBoundsHumid;

    private OutOfBoundsAnalogRepository $outOfBoundsAnalog;

    public function __construct(
      OutOfBoundsTempORMRepository $outBoundsTempORMRepository,
      OutOfBoundsHumidityRepository $outOfBoundsHumidORMRepository,
      OutOfBoundsAnalogRepository $outOfBoundsAnalogORMRepository,
    ) {
        $this->outOfBoundsTemp = $outBoundsTempORMRepository;
        $this->outOfBoundsHumid = $outOfBoundsHumidORMRepository;
        $this->outOfBoundsAnalog = $outOfBoundsAnalogORMRepository;
    }

    public function getOutOfBoundsServiceRepository(string $sensorReadingType): OutOfBoundsRepositoryInterface
    {
        return match ($sensorReadingType) {
            Temperature::READING_TYPE => $this->outOfBoundsTemp,
            Humidity::READING_TYPE => $this->outOfBoundsHumid,
            Analog::READING_TYPE => $this->outOfBoundsAnalog,
            default => throw new ReadingTypeNotSupportedException(
                ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_UPDATE_APP_MESSAGE
            )
        };
    }
}
