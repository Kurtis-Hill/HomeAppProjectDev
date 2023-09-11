<?php

namespace App\Sensors\Factories\SensorType;

use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Repository\SensorType\ORM\BmpRepository;
use App\Sensors\Repository\SensorType\ORM\DallasRepository;
use App\Sensors\Repository\SensorType\ORM\DhtRepository;
use App\Sensors\Repository\SensorType\ORM\GenericMotionRepository;
use App\Sensors\Repository\SensorType\ORM\GenericRelayRepository;
use App\Sensors\Repository\SensorType\ORM\GenericSensorTypeRepositoryInterface;
use App\Sensors\Repository\SensorType\ORM\LDRRepository;
use App\Sensors\Repository\SensorType\ORM\SoilRepository;

class SensorTypeRepositoryFactory
{
    private DallasRepository $dallasRepository;

    private BmpRepository $bmpRepository;

    private SoilRepository $soilRepository;

    private DhtRepository $dhtRepository;

    private GenericMotionRepository $genericMotionRepository;

    private GenericRelayRepository $genericRelayRepository;

    private LDRRepository $ldrRepository;

    public function __construct(
        DallasRepository $dallasRepository,
        BmpRepository $bmpRepository,
        SoilRepository $soilRepository,
        DhtRepository $dhtRepository,
        GenericMotionRepository $genericMotionRepository,
        GenericRelayRepository $genericRelayRepository,
        LDRRepository $ldrRepository,
    ) {
        $this->dallasRepository = $dallasRepository;
        $this->bmpRepository = $bmpRepository;
        $this->soilRepository = $soilRepository;
        $this->dhtRepository = $dhtRepository;
        $this->genericMotionRepository = $genericMotionRepository;
        $this->genericRelayRepository = $genericRelayRepository;
        $this->ldrRepository = $ldrRepository;
    }

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
            default => throw new SensorTypeException(sprintf(SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED, $sensorType)),
        };
    }
}
