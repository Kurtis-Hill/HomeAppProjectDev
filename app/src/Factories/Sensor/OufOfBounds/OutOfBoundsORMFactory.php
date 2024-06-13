<?php

namespace App\Factories\Sensor\OufOfBounds;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Exceptions\Sensor\ReadingTypeNotSupportedException;
use App\Repository\Sensor\OutOfBounds\ORM\OutOfBoundsAnalogRepository;
use App\Repository\Sensor\OutOfBounds\ORM\OutOfBoundsHumidityRepository;
use App\Repository\Sensor\OutOfBounds\ORM\OutOfBoundsLatitudeRepository;
use App\Repository\Sensor\OutOfBounds\ORM\OutOfBoundsTempRepository;
use App\Repository\Sensor\OutOfBounds\OutOfBoundsRepositoryInterface;

class OutOfBoundsORMFactory implements OutOfBoundsFactoryInterface
{
    private OutOfBoundsTempRepository $outOfBoundsTemp;

    private OutOfBoundsHumidityRepository $outOfBoundsHumid;

    private OutOfBoundsAnalogRepository $outOfBoundsAnalog;

    private OutOfBoundsLatitudeRepository $outOfBoundsLatitude;

    public function __construct(
        OutOfBoundsTempRepository $outBoundsTempORMRepository,
        OutOfBoundsHumidityRepository $outOfBoundsHumidORMRepository,
        OutOfBoundsAnalogRepository $outOfBoundsAnalogORMRepository,
        OutOfBoundsLatitudeRepository $outOfBoundsLatitudeORMRepository,
    ) {
        $this->outOfBoundsTemp = $outBoundsTempORMRepository;
        $this->outOfBoundsHumid = $outOfBoundsHumidORMRepository;
        $this->outOfBoundsAnalog = $outOfBoundsAnalogORMRepository;
        $this->outOfBoundsLatitude = $outOfBoundsLatitudeORMRepository;
    }

    /**
     * @throws ReadingTypeNotSupportedException
     */
    public function getOutOfBoundsServiceRepository(string $sensorReadingType): OutOfBoundsRepositoryInterface
    {
        return match ($sensorReadingType) {
            Temperature::getReadingTypeName() => $this->outOfBoundsTemp,
            Humidity::getReadingTypeName() => $this->outOfBoundsHumid,
            Analog::getReadingTypeName() => $this->outOfBoundsAnalog,
            Latitude::getReadingTypeName() => $this->outOfBoundsLatitude,
            default => throw new ReadingTypeNotSupportedException(
                ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_UPDATE_APP_MESSAGE
            )
        };
    }
}
