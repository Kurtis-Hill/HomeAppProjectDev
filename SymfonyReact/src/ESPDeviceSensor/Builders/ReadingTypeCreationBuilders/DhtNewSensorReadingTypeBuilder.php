<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use Exception;

class DhtNewSensorReadingTypeBuilder extends AbstractSensorReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    public function buildNewReadingTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $dht = new Dht();
        $this->setSensorObject($dht, $sensor);
//        try {
            $this->buildTemperatureSensor($dht);
            $this->buildHumiditySensor($dht);

//            $this->sensorReadingTypeFactory->flush();
//        } catch (Exception $e) {
////            dd($e);
//            throw new SensorTypeBuilderFailureException(
//                sprintf(
//                    SensorTypeBuilderFailureException::SENSOR_TYPE_BUILDER_FAILURE_MESSAGE,
//                    $sensor->getSensorTypeObject()->getSensorType()
//                )
//            );
//        }

        return $dht;
    }
}