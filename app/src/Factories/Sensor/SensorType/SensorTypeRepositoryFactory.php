<?php

namespace App\Factories\Sensor\SensorType;

use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Exceptions\Sensor\SensorTypeException;
use App\Repository\Sensor\SensorType\ORM\BmpRepository;
use App\Repository\Sensor\SensorType\ORM\DallasRepository;
use App\Repository\Sensor\SensorType\ORM\DhtRepository;
use App\Repository\Sensor\SensorType\ORM\GenericMotionRepository;
use App\Repository\Sensor\SensorType\ORM\GenericRelayRepository;
use App\Repository\Sensor\SensorType\ORM\GenericSensorTypeRepositoryInterface;
use App\Repository\Sensor\SensorType\ORM\LDRRepository;
use App\Repository\Sensor\SensorType\ORM\ShtRepository;
use App\Repository\Sensor\SensorType\ORM\SoilRepository;

readonly class SensorTypeRepositoryFactory
{
    public function __construct(
        private DallasRepository $dallasRepository,
        private BmpRepository $bmpRepository,
        private SoilRepository $soilRepository,
        private DhtRepository $dhtRepository,
        private GenericMotionRepository $genericMotionRepository,
        private GenericRelayRepository $genericRelayRepository,
        private LDRRepository $ldrRepository,
        private ShtRepository $shtRepository,
    ) {}

    /**
     * @throws \App\Exceptions\Sensor\SensorTypeException
     */
    public function getSensorTypeRepository(string $sensorType): GenericSensorTypeRepositoryInterface
    {
        return match ($sensorType) {
            Dallas::NAME => $this->dallasRepository,
            Bmp::NAME => $this->bmpRepository,
            Soil::NAME => $this->soilRepository,
            Dht::NAME => $this->dhtRepository,
            GenericMotion::NAME => $this->genericMotionRepository,
            GenericRelay::NAME => $this->genericRelayRepository,
            LDR::NAME => $this->ldrRepository,
            Sht::NAME => $this->shtRepository,
            default => throw new SensorTypeException(sprintf(SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED, $sensorType)),
        };
    }
}
