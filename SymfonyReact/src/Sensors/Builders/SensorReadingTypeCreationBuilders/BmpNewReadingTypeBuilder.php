<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders;

use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\HumidityReadingTypeObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\LatitudeReadingTypeObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\TemperatureReadingTypeObjectBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;

class BmpNewReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    private TemperatureReadingTypeObjectBuilder $temperatureReadingTypeObjectBuilder;

    private HumidityReadingTypeObjectBuilder $humidityReadingTypeObjectBuilder;

    private LatitudeReadingTypeObjectBuilder $latitudeReadingTypeObjectBuilder;

    public function __construct(
        TemperatureReadingTypeObjectBuilder $temperatureReadingTypeObjectBuilder,
        HumidityReadingTypeObjectBuilder $humidityReadingTypeObjectBuilder,
        LatitudeReadingTypeObjectBuilder $latitudeReadingTypeObjectBuilder,
    ) {
        $this->temperatureReadingTypeObjectBuilder = $temperatureReadingTypeObjectBuilder;
        $this->humidityReadingTypeObjectBuilder = $humidityReadingTypeObjectBuilder;
        $this->latitudeReadingTypeObjectBuilder = $latitudeReadingTypeObjectBuilder;
    }

    public function buildNewSensorTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $bmp = new Bmp();
        $bmp->setSensor($sensor);
        $this->temperatureReadingTypeObjectBuilder->buildReadingTypeObject($bmp);
        $this->humidityReadingTypeObjectBuilder->buildReadingTypeObject($bmp);
        $this->latitudeReadingTypeObjectBuilder->buildReadingTypeObject($bmp);

        return $bmp;
    }
}
