<?php

namespace App\Sensors\Factories\ORMFactories\OufOfBounds;

use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Repository\ORM\OutOfBounds\OutOfBoundsHumidityRepository;
use App\Sensors\Repository\ORM\OutOfBounds\OutOfBoundsLatitudeRepository;
use App\Sensors\Repository\ORM\OutOfBounds\OutOfBoundsRepositoryInterface;
use App\Sensors\Repository\ORM\OutOfBounds\OutOfBoundsAnalogRepository;
use App\Sensors\Repository\ORM\OutOfBounds\OutOfBoundsTempORMRepository;

class OutOfBoundsORMFactory
{
    private OutOfBoundsTempORMRepository $outOfBoundsTemp;

    private OutOfBoundsHumidityRepository $outOfBoundsHumid;

    private OutOfBoundsAnalogRepository $outOfBoundsAnalog;

    private OutOfBoundsLatitudeRepository $outOfBoundsLatitude;

    public function __construct(
        OutOfBoundsTempORMRepository $outBoundsTempORMRepository,
        OutOfBoundsHumidityRepository $outOfBoundsHumidORMRepository,
        OutOfBoundsAnalogRepository $outOfBoundsAnalogORMRepository,
        OutOfBoundsLatitudeRepository $outOfBoundsLatitudeORMRepository,
    ) {
        $this->outOfBoundsTemp = $outBoundsTempORMRepository;
        $this->outOfBoundsHumid = $outOfBoundsHumidORMRepository;
        $this->outOfBoundsAnalog = $outOfBoundsAnalogORMRepository;
        $this->outOfBoundsLatitude = $outOfBoundsLatitudeORMRepository;
    }

    public function getOutOfBoundsServiceRepository(string $sensorReadingType): OutOfBoundsRepositoryInterface
    {
        return match ($sensorReadingType) {
            Temperature::READING_TYPE => $this->outOfBoundsTemp,
            Humidity::READING_TYPE => $this->outOfBoundsHumid,
            Analog::READING_TYPE => $this->outOfBoundsAnalog,
            Latitude::READING_TYPE => $this->outOfBoundsLatitude,
            default => throw new ReadingTypeNotSupportedException(
                ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_UPDATE_APP_MESSAGE
            )
        };
    }
}
