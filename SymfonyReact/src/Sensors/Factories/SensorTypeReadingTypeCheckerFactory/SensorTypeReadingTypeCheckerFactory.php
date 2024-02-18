<?php

namespace App\Sensors\Factories\SensorTypeReadingTypeCheckerFactory;

use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\SensorTypeNotFoundException;
use App\Sensors\SensorServices\SensorTypeReadingTypeChecker\BmpReadingTypeChecker;
use App\Sensors\SensorServices\SensorTypeReadingTypeChecker\DallasReadingTypeChecker;
use App\Sensors\SensorServices\SensorTypeReadingTypeChecker\DhtReadingTypeChecker;
use App\Sensors\SensorServices\SensorTypeReadingTypeChecker\SensorTypeReadingTypeInterface;
use App\Sensors\SensorServices\SensorTypeReadingTypeChecker\SoilReadingTypeChecker;

class SensorTypeReadingTypeCheckerFactory
{
    private DhtReadingTypeChecker $dhtReadingTypeChecker;

    private BmpReadingTypeChecker $bmpReadingTypeChecker;

    private DallasReadingTypeChecker $dallasReadingTypeChecker;

    private SoilReadingTypeChecker $soilReadingTypeChecker;

    public function __construct(
        DhtReadingTypeChecker $dhtReadingTypeChecker,
        BmpReadingTypeChecker $bmpReadingTypeChecker,
        DallasReadingTypeChecker $dallasReadingTypeChecker,
        SoilReadingTypeChecker $soilReadingTypeChecker
    ) {
        $this->dhtReadingTypeChecker = $dhtReadingTypeChecker;
        $this->bmpReadingTypeChecker = $bmpReadingTypeChecker;
        $this->dallasReadingTypeChecker = $dallasReadingTypeChecker;
        $this->soilReadingTypeChecker = $soilReadingTypeChecker;
    }


    /**
     * @throws SensorTypeNotFoundException
     */
    public function fetchSensorReadingTypeChecker(string $sensorType): SensorTypeReadingTypeInterface
    {
        return match ($sensorType) {
            Dht::NAME => $this->dhtReadingTypeChecker,
            Bmp::NAME => $this->bmpReadingTypeChecker,
            Dallas::NAME => $this->dallasReadingTypeChecker,
            Soil::NAME => $this->soilReadingTypeChecker,
            default => throw new SensorTypeNotFoundException(
                sprintf(SensorTypeNotFoundException::SENSOR_TYPE_NOT_RECOGNISED, $sensorType)
            )
        };
    }
}
