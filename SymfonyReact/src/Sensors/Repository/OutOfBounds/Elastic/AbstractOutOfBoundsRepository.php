<?php

namespace App\Sensors\Repository\OutOfBounds\Elastic;


use Elastica\Index;

class AbstractOutOfBoundsRepository
{
    protected Index $index;

    public const OUTBOUNDS_INDICES = [
        OutOfBoundsAnalogRepository::ES_INDEX => [
            'sensorFieldName' => 'analogID',
            'sensorReading' => 'float',
        ],
        OutOfBoundsTempRepository::ES_INDEX => [
            'sensorFieldName' => 'tempID',
            'sensorReading' => 'float',
        ],
        OutOfBoundsHumidityRepository::ES_INDEX => [
            'sensorFieldName' => 'humidID',
            'sensorReading' => 'float',
        ],
        OutOfBoundsLatitudeRepository::ES_INDEX => [
            'sensorFieldName' => 'latID',
            'sensorReading' => 'double',
        ],
    ];

    public function __construct(Index $index)
    {
        $this->index = $index;
    }
}
