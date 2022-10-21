<?php

namespace App\Sensors\Factories\OufOfBounds;

use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Repository\OutOfBounds\Elastic\OutOfBoundsAnalogRepository;
use App\Sensors\Repository\OutOfBounds\Elastic\OutOfBoundsHumidityRepository;
use App\Sensors\Repository\OutOfBounds\Elastic\OutOfBoundsLatitudeRepository;
use App\Sensors\Repository\OutOfBounds\Elastic\OutOfBoundsTempRepository;
use App\Sensors\Repository\OutOfBounds\OutOfBoundsRepositoryInterface;

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
