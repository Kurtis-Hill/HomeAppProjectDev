<?php

declare(strict_types=1);

namespace App\DTOs\Sensor\Request\SensorUpdateDTO;

use App\DTOs\Device\Request\DeviceRequest\DeviceRequestDTOInterface;
use App\Entity\Sensor\AbstractSensorType;
use Symfony\Component\Validator\Constraints as Assert;

readonly class SensorDeletionDTO implements DeviceRequestDTOInterface
{
    public function __construct(
        #[Assert\Choice(
            AbstractSensorType::ALL_SENSOR_TYPES
        )]
        private string $sensorType
    ) {
    }

    public function getSensorType(): string
    {
        return strtolower($this->sensorType);
    }
}
