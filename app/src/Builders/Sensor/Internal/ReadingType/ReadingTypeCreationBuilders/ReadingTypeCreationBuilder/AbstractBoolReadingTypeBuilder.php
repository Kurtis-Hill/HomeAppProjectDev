<?php
declare(strict_types=1);

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Exceptions\Sensor\SensorTypeException;
use App\Factories\Sensor\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Repository\Sensor\ReadingType\ORM\BaseSensorReadingTypeRepository;
use App\Repository\Sensor\SensorReadingType\ORM\BoolReadingBaseSensorRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

abstract class AbstractBoolReadingTypeBuilder extends AbstractReadingTypeBuilder
{
    protected SensorReadingTypeRepositoryFactory $sensorReadingTypeRepositoryFactory;

    public function __construct(
        BaseReadingTypeBuilder $baseSensorReadingType,
        BaseSensorReadingTypeRepository $baseSensorReadingTypeRepository,
        SensorReadingTypeRepositoryFactory $sensorReadingTypeRepositoryFactory,
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

        if ($boolObject instanceof AllSensorReadingTypeInterface) {
            $readingTypeRepository->persist($boolObject);
        } else {
            throw new SensorTypeException();
        }
    }
}
