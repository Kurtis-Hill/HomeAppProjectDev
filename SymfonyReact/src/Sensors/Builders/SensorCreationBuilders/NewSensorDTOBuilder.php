<?php

namespace App\Sensors\Builders\SensorCreationBuilders;

use App\Devices\Entity\Devices;
use App\Sensors\DTO\Internal\Sensor\NewSensorDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use Symfony\Component\Security\Core\User\UserInterface;

class NewSensorDTOBuilder
{
    public static function buildNewSensorDTO(
        string $sensorName,
        SensorType $sensorType,
        Devices $device,
        UserInterface $user,
        int $pinNumber,
    ): NewSensorDTO {
        $newSensor = new Sensor();

        return new NewSensorDTO(
            $sensorName,
            $sensorType,
            $device,
            $user,
            $newSensor,
            $pinNumber,
        );
    }
}
