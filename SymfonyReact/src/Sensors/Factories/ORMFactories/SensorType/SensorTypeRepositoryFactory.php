<?php

namespace App\Sensors\Factories\ORMFactories\SensorType;

use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Repository\ORM\SensorType\BmpRepository;
use App\Sensors\Repository\ORM\SensorType\DallasRepository;
use App\Sensors\Repository\ORM\SensorType\DhtRepository;
use App\Sensors\Repository\ORM\SensorType\GenericSensorTypeRepositoryInterface;
use App\Sensors\Repository\ORM\SensorType\SoilRepository;

class SensorTypeRepositoryFactory
{
    private DallasRepository $dallasRepository;

    private BmpRepository $bmpRepository;

    private SoilRepository $soilRepository;

    private DhtRepository $dhtRepository;

    public function __construct(
        DallasRepository $dallasRepository,
        BmpRepository $bmpRepository,
        SoilRepository $soilRepository,
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
