<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Sensors\Repository\ReadingType\ORM\BaseSensorReadingTypeRepository;
use DateTimeImmutable;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

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
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws SensorReadingTypeRepositoryFactoryException
     */
    protected function setBoolDefaults(
        Sensor $sensor,
        BoolReadingSensorInterface $boolObject,
        float|int|bool $currentReading,
        bool $constRecord
    ): void {
        $baseReadingType = $this->createNewBaseReadingTypeObject($sensor);
        $boolObject->setBaseReadingType($baseReadingType);
        $boolObject->setCurrentReading($currentReading);
//        $boolObject->setSensor($sensor);
//        $boolObject->setCreatedAt();
        $boolObject->setRequestedReading($currentReading);
        $boolObject->setConstRecord($constRecord);
//        $boolObject->setUpdatedAt();

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
