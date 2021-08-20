<?php


namespace App\DTOs\CardDTOs\Builders\SensorTypeBuilders;


use App\DTOs\CardDTOs\Builders\CardBuilderDTOInterface;
use App\DTOs\CardDTOs\Sensors\DTOs\AllCardViewDTOInterface;
use App\HomeAppSensorCore\Interfaces\SensorInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use Symfony\Component\Intl\Exception\NotImplementedException;

class SensorTypeCardViewGraphBuilder implements CardBuilderDTOInterface
{
    public function makeDTO(SensorInterface $sensorData, array $extraSensorData = []): AllCardViewDTOInterface
    {
        throw new NotImplementedException('makeDTO not implemented yet');
    }

    protected function setStandardSensorData(StandardReadingSensorInterface $sensorTypeObject, string $type, string $symbol = null): array
    {
        throw new NotImplementedException('setStandardSensorData not implemented yet');
    }
}
