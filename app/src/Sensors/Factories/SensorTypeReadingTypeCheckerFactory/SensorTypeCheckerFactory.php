<?php

namespace App\Sensors\Factories\SensorTypeReadingTypeCheckerFactory;

use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\SensorTypeNotFoundException;
use App\Sensors\SensorServices\SensorTypeReadingTypeChecker\BmpReadingTypeChecker;
use App\Sensors\SensorServices\SensorTypeReadingTypeChecker\DallasReadingTypeChecker;
use App\Sensors\SensorServices\SensorTypeReadingTypeChecker\DhtReadingTypeChecker;
use App\Sensors\SensorServices\SensorTypeReadingTypeChecker\GenericMotionReadingTypeChecker;
use App\Sensors\SensorServices\SensorTypeReadingTypeChecker\GenericRelayReadingTypeChecker;
use App\Sensors\SensorServices\SensorTypeReadingTypeChecker\LDRReadingTypeChecker;
use App\Sensors\SensorServices\SensorTypeReadingTypeChecker\SensorTypeReadingTypeInterface;
use App\Sensors\SensorServices\SensorTypeReadingTypeChecker\ShtReadingTypeChecker;
use App\Sensors\SensorServices\SensorTypeReadingTypeChecker\SoilReadingTypeChecker;

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
