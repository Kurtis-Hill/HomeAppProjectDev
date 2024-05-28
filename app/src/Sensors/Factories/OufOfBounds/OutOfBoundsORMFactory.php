<?php

namespace App\Sensors\Factories\OufOfBounds;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Repository\OutOfBounds\ORM\OutOfBoundsAnalogRepository;
use App\Sensors\Repository\OutOfBounds\ORM\OutOfBoundsHumidityRepository;
use App\Sensors\Repository\OutOfBounds\ORM\OutOfBoundsLatitudeRepository;
use App\Sensors\Repository\OutOfBounds\ORM\OutOfBoundsTempRepository;
use App\Sensors\Repository\OutOfBounds\OutOfBoundsRepositoryInterface;

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
