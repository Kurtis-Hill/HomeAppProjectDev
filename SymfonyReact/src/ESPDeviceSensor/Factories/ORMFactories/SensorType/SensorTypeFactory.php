<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\SensorType;

use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\ESPDeviceSensor\Repository\ORM\SensorType\BmpRepository;
use App\ESPDeviceSensor\Repository\ORM\SensorType\DallasRepository;
use App\ESPDeviceSensor\Repository\ORM\SensorType\DhtRepository;
use App\ESPDeviceSensor\Repository\ORM\SensorType\SensorTypeRepositoryInterface;
use App\ESPDeviceSensor\Repository\ORM\SensorType\SoilRepository;

class SensorTypeFactory implements SensorTypeFactoryInterface
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

    public function getSensorTypeRepository(string $sensorType): SensorTypeRepositoryInterface
    {
        return match ($sensorType) {
            Dallas::class => $this->dallasRepository,
            Bmp::class => $this->bmpRepository,
            Soil::class => $this->soilRepository,
            Dht::class => $this->dhtRepository,
        };
    }
}
