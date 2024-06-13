<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders;

use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\Sensor;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Exceptions\Sensor\SensorTypeException;
use JetBrains\PhpStorm\ArrayShape;

interface NewSensorReadingTypeBuilderInterface
{
    /**
     * @throws \App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException
     * @throws SensorTypeException
     */
    #[ArrayShape([Temperature::class|Humidity::class|Latitude::class|Analog::class|Relay::class|Motion::class])]
    public function buildNewSensorTypeObjects(Sensor $sensor): array;
}
