<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use DateTimeImmutable;

abstract class AbstractBoolObjectBuilder
{
    protected SensorReadingTypeRepositoryFactory $sensorReadingTypeRepositoryFactory;

    public function __construct(SensorReadingTypeRepositoryFactory $sensorReadingTypeRepositoryFactory)
    {
        $this->sensorReadingTypeRepositoryFactory = $sensorReadingTypeRepositoryFactory;
    }

    /**
     * @throws SensorTypeException
     */
    protected function setBoolDefaults(
        SensorTypeInterface $sensorTypeObject,
        AllSensorReadingTypeInterface $boolObject,
        float|int|bool $currentReading,
        bool $constRecord
    ): void {
        if (!$boolObject instanceof BoolReadingSensorInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $now = new DateTimeImmutable('now');
        $boolObject->setCurrentReading($currentReading);
        $boolObject->setSensor($sensorTypeObject->getSensor());
        $boolObject->setCreatedAt(clone $now);
        $boolObject->setRequestedReading($currentReading);
        $boolObject->setConstRecord($constRecord);
        $boolObject->setUpdatedAt();

        if ($sensorTypeObject instanceof MotionSensorReadingTypeInterface) {
            if (!$boolObject instanceof Motion) {
                throw new SensorTypeException(
                    SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
                );
            }
            $sensorTypeObject->setMotion($boolObject);
        }
        if ($sensorTypeObject instanceof RelayReadingTypeInterface) {
            if (!$boolObject instanceof Relay) {
                throw new SensorTypeException(
                    SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
                );
            }
            $sensorTypeObject->setRelay($boolObject);
        }

        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($boolObject::getReadingTypeName());

        $readingTypeRepository->persist($boolObject);
    }
}
