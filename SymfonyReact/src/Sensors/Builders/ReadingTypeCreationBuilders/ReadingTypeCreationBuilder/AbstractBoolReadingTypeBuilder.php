<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Sensors\Repository\ReadingType\ORM\BaseSensorReadingTypeRepository;
use DateTimeImmutable;

abstract class AbstractBoolReadingTypeBuilder extends AbstractReadingTypeBuilder
{
    protected SensorReadingTypeRepositoryFactory $sensorReadingTypeRepositoryFactory;


    public function __construct(
        BaseReadingTypeBuilder $baseSensorReadingType,
        BaseSensorReadingTypeRepository $baseSensorReadingTypeRepository,
        SensorReadingTypeRepositoryFactory $sensorReadingTypeRepositoryFactory
    ) {
        $this->sensorReadingTypeRepositoryFactory = $sensorReadingTypeRepositoryFactory;
        parent::__construct($baseSensorReadingType, $baseSensorReadingTypeRepository);
    }

    /**
     * @throws SensorTypeException
     */
    protected function setBoolDefaults(
        Sensor $sensor,
        AllSensorReadingTypeInterface $boolObject,
        float|int|bool $currentReading,
        bool $constRecord
    ): void {
        if (!$boolObject instanceof BoolReadingSensorInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $baseReadingType = $this->createNewBaseReadingTypeObject();
        $boolObject->setBaseReadingType($baseReadingType);
        $now = new DateTimeImmutable('now');
        $boolObject->setCurrentReading($currentReading);
        $boolObject->setSensor($sensor);
        $boolObject->setCreatedAt(clone $now);
        $boolObject->setRequestedReading($currentReading);
        $boolObject->setConstRecord($constRecord);
        $boolObject->setUpdatedAt();

        if (($sensor instanceof MotionSensorReadingTypeInterface) && !$boolObject instanceof Motion) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        if (($sensor instanceof RelayReadingTypeInterface) && !$boolObject instanceof Relay) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }

        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($boolObject::getReadingTypeName());

        $readingTypeRepository->persist($boolObject);
    }
}
