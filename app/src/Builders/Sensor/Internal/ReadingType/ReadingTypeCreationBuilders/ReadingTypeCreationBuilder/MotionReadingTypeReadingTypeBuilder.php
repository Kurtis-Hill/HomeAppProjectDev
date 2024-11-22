<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Exceptions\Sensor\SensorTypeException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class MotionReadingTypeReadingTypeBuilder extends AbstractBoolReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    /**
     * @throws OptimisticLockException
     * @throws SensorTypeException
     * @throws ORMException
     * @throws SensorReadingTypeRepositoryFactoryException
     */
    public function buildReadingTypeObject(Sensor $sensor): AllSensorReadingTypeInterface
    {
        $sensorType = $sensor->getSensorTypeObject();
        if (!$sensorType instanceof MotionSensorReadingTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $motionSensor = new Motion();

        $this->setBoolDefaults(
            $sensor,
            $motionSensor,
            false,
            false
        );

        return $motionSensor;
    }
}
