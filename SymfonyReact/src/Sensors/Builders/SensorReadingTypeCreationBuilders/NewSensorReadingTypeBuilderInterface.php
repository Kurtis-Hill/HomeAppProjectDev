<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorTypeException;
use JetBrains\PhpStorm\ArrayShape;

interface NewSensorReadingTypeBuilderInterface
{
    /**
     * @throws SensorReadingTypeRepositoryFactoryException
     * @throws SensorTypeException
     */
    #[ArrayShape([Temperature::class|Humidity::class|Latitude::class|Analog::class|Relay::class|Motion::class])]
    public function buildNewSensorTypeObjects(Sensor $sensor): array;
}
