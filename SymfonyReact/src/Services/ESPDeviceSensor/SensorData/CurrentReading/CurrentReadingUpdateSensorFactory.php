<?php

namespace App\Services\ESPDeviceSensor\SensorData\CurrentReading;

use App\Entity\Sensors\SensorType;
use App\Services\ESPDeviceSensor\SensorData\CurrentReading\Sensors\AllSensorUpdateReadingServiceInterface;
use App\Services\ESPDeviceSensor\SensorData\CurrentReading\Sensors\BmpCurrentReadingUpdate;
use App\Services\ESPDeviceSensor\SensorData\CurrentReading\Sensors\DallasCurrentReadingUpdate;
use App\Services\ESPDeviceSensor\SensorData\CurrentReading\Sensors\DhtCurrentReadingUpdate;
use UnexpectedValueException;

class CurrentReadingUpdateSensorFactory
{
    private DallasCurrentReadingUpdate $dallasCurrentReadingUpdateService;

    private DhtCurrentReadingUpdate $dhtCurrentReadingUpdate;

    private BmpCurrentReadingUpdate $bmpCurrentReadingUpdate;

    public function getUpdateSensorCurrentReadingService(string $sensorType): AllSensorUpdateReadingServiceInterface
    {
        return match ($sensorType) {
            SensorType::DALLAS_TEMPERATURE => $this->dallasCurrentReadingUpdateService,
            SensorType::DHT_SENSOR => $this->dhtCurrentReadingUpdate,
            SensorType::BMP_SENSOR => $this->bmpCurrentReadingUpdate,
            default => throw new UnexpectedValueException('No type has been added to handle this request')
        };
    }

    public function setDhtReadingUpdateService(DhtCurrentReadingUpdate $dhtCurrentReadingUpdate)
    {
        $this->dhtCurrentReadingUpdate = $dhtCurrentReadingUpdate;
    }

    public function setDallasReadingUpdateService(DallasCurrentReadingUpdate $dallasCurrentReadingUpdateService): void
    {
        $this->dallasCurrentReadingUpdateService = $dallasCurrentReadingUpdateService;
    }

    public function setBmpReadingUpdateService(BmpCurrentReadingUpdate $bmpCurrentReadingUpdate)
    {
        $this->bmpCurrentReadingUpdate = $bmpCurrentReadingUpdate;
    }
}
