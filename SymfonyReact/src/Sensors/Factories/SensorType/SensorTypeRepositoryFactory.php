<?php

namespace App\Sensors\Factories\SensorType;

use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Repository\SensorType\ORM\BmpRepository;
use App\Sensors\Repository\SensorType\ORM\DallasRepository;
use App\Sensors\Repository\SensorType\ORM\DhtRepository;
use App\Sensors\Repository\SensorType\ORM\GenericSensorTypeRepositoryInterface;
use App\Sensors\Repository\SensorType\ORM\SoilRepository;

class SensorTypeRepositoryFactory
{
    private DallasRepository $dallasRepository;

    private BmpRepository $bmpRepository;

    private SoilRepository $soilRepository;

    private DhtRepository $dhtRepository;

    public function __construct(
        DallasRepository $dallasRepository,
        BmpRepository $bmpRepository,
        \App\Sensors\Repository\SensorType\ORM\SoilRepository $soilRepository,
        DhtRepository $dhtRepository,
    ) {
        $this->dallasRepository = $dallasRepository;
        $this->bmpRepository = $bmpRepository;
        $this->soilRepository = $soilRepository;
        $this->dhtRepository = $dhtRepository;
    }

    public function getSensorTypeRepository(string $sensorType): GenericSensorTypeRepositoryInterface
    {
        return match ($sensorType) {
            Dallas::NAME => $this->dallasRepository,
            Bmp::NAME => $this->bmpRepository,
            Soil::NAME => $this->soilRepository,
            Dht::NAME => $this->dhtRepository,
            default => throw new SensorTypeException(sprintf(SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED, $sensorType)),
        };
    }
}
