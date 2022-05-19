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

class SensorTypeRepositoryFactory implements SensorTypeRepositroyFactoryInterface
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
            Dallas::class => $this->dallasRepository,
            Bmp::class => $this->bmpRepository,
            Soil::class => $this->soilRepository,
            Dht::class => $this->dhtRepository,
            default => throw new SensorTypeException(SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME)
        };
    }
}
