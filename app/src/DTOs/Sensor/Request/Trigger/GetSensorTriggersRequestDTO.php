<?php
declare(strict_types=1);

namespace App\DTOs\Sensor\Request\Trigger;

use App\DTOs\RequestDTO;
use Symfony\Component\Validator\Constraints as Assert;

class GetSensorTriggersRequestDTO extends RequestDTO
{
    #[
        Assert\Type(
            type: ['int', 'null'],
            message: 'sensorID must be an {{ type }}, you have provided {{ value }}'
        ),
        Assert\Positive(
            message: 'sensorID must be a positive integer, you have provided {{ value }}'
        )
    ]
    private ?int $sensorID = null;

    public function getSensorID(): ?int
    {
        return $this->sensorID;
    }

    public function setSensorID(?int $sensorID): void
    {
        $this->sensorID = $sensorID;
    }
}
