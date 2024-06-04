<?php

namespace App\Sensors\Factories\SensorType;

use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Repository\SensorType\ORM\BmpRepository;
use App\Sensors\Repository\SensorType\ORM\DallasRepository;
use App\Sensors\Repository\SensorType\ORM\DhtRepository;
use App\Sensors\Repository\SensorType\ORM\GenericMotionRepository;
use App\Sensors\Repository\SensorType\ORM\GenericRelayRepository;
use App\Sensors\Repository\SensorType\ORM\GenericSensorTypeRepositoryInterface;
use App\Sensors\Repository\SensorType\ORM\LDRRepository;
use App\Sensors\Repository\SensorType\ORM\ShtRepository;
use App\Sensors\Repository\SensorType\ORM\SoilRepository;

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
     * @throws SensorTypeException
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
