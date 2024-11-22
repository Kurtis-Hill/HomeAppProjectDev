<?php

namespace App\Factories\Sensor\SensorTypeReadingTypeCheckerFactory;

use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Exceptions\Sensor\SensorTypeNotFoundException;
use App\Services\Sensor\SensorTypeReadingTypeChecker\BmpReadingTypeChecker;
use App\Services\Sensor\SensorTypeReadingTypeChecker\DallasReadingTypeChecker;
use App\Services\Sensor\SensorTypeReadingTypeChecker\DhtReadingTypeChecker;
use App\Services\Sensor\SensorTypeReadingTypeChecker\GenericMotionReadingTypeChecker;
use App\Services\Sensor\SensorTypeReadingTypeChecker\GenericRelayReadingTypeChecker;
use App\Services\Sensor\SensorTypeReadingTypeChecker\LDRReadingTypeChecker;
use App\Services\Sensor\SensorTypeReadingTypeChecker\SensorTypeReadingTypeInterface;
use App\Services\Sensor\SensorTypeReadingTypeChecker\ShtReadingTypeChecker;
use App\Services\Sensor\SensorTypeReadingTypeChecker\SoilReadingTypeChecker;

readonly class SensorTypeCheckerFactory
{
    public function __construct(
        private DhtReadingTypeChecker $dhtReadingTypeChecker,
        private BmpReadingTypeChecker $bmpReadingTypeChecker,
        private DallasReadingTypeChecker $dallasReadingTypeChecker,
        private SoilReadingTypeChecker $soilReadingTypeChecker,
        private GenericMotionReadingTypeChecker $genericMotionReadingTypeChecker,
        private GenericRelayReadingTypeChecker $genericRelayReadingTypeChecker,
        private LDRReadingTypeChecker $ldrReadingTypeChecker,
        private ShtReadingTypeChecker $shtReadingTypeChecker
    ) {}


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
            GenericMotion::NAME => $this->genericMotionReadingTypeChecker,
            GenericRelay::NAME => $this->genericRelayReadingTypeChecker,
            LDR::NAME => $this->ldrReadingTypeChecker,
            Sht::NAME => $this->shtReadingTypeChecker,
            default => throw new SensorTypeNotFoundException(
                sprintf(SensorTypeNotFoundException::SENSOR_TYPE_NOT_RECOGNISED, $sensorType)
            )
        };
    }
}
