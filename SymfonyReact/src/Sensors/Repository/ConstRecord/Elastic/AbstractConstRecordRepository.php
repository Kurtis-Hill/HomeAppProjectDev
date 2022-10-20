<?php

namespace App\Sensors\Repository\ConstRecord\Elastic;


class AbstractConstRecordRepository
{
    public const CONST_RECORD_INDICES = [
        ConstRecordLatitudeRepository::ES_INDEX => [
            'sensorFieldName' => 'latID',
            'sensorReading' => 'double',
        ],
        ConstRecordTemperatureRepository::ES_INDEX => [
            'sensorFieldName' => 'tempID',
            'sensorReading' => 'float',
        ],
        ConstRecordHumidityRepository::ES_INDEX => [
            'sensorFieldName' => 'humidID',
            'sensorReading' => 'float',
        ],
        ConstRecordAnalogRepository::ES_INDEX => [
            'sensorFieldName' => 'analogID',
            'sensorReading' => 'float',
        ],
    ];

}
