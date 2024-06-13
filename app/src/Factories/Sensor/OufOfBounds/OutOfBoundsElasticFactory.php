<?php

namespace App\Factories\Sensor\OufOfBounds;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Exceptions\Sensor\ReadingTypeNotSupportedException;
use App\Repository\Sensor\OutOfBounds\Elastic\OutOfBoundsAnalogRepository;
use App\Repository\Sensor\OutOfBounds\Elastic\OutOfBoundsHumidityRepository;
use App\Repository\Sensor\OutOfBounds\Elastic\OutOfBoundsLatitudeRepository;
use App\Repository\Sensor\OutOfBounds\Elastic\OutOfBoundsTempRepository;
use App\Repository\Sensor\OutOfBounds\OutOfBoundsRepositoryInterface;

class OutOfBoundsElasticFactory implements OutOfBoundsFactoryInterface
{
    private OutOfBoundsTempRepository $outOfBoundsTemp;

    private OutOfBoundsHumidityRepository $outOfBoundsHumid;

    private OutOFBoundsAnalogRepository $outOfBoundsAnalog;

    private OutOfBoundsLatitudeRepository $outOfBoundsLatitude;

    public function __construct(
        OutOfBoundsTempRepository $outBoundsTempElasticRepository,
        OutOfBoundsHumidityRepository $outOfBoundsHumidElasticRepository,
        OutOfBoundsAnalogRepository $outOfBoundsAnalogElasticRepository,
        OutOfBoundsLatitudeRepository $outOfBoundsLatitudeElasticRepository,

    ) {
        $this->outOfBoundsTemp = $outBoundsTempElasticRepository;
        $this->outOfBoundsHumid = $outOfBoundsHumidElasticRepository;
        $this->outOfBoundsAnalog = $outOfBoundsAnalogElasticRepository;
        $this->outOfBoundsLatitude = $outOfBoundsLatitudeElasticRepository;
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
