<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor\ReadingTypeCreation;

use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingTypeFactoryInterface;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorType\SensorTypeFactoryInterface;
use App\HomeAppSensorCore\Interfaces\APIErrorInterface;
use App\HomeAppSensorCore\Interfaces\SensorInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use DateTime;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ReadingTypeCreationService implements SensorReadingTypeCreationInterface, APIErrorInterface
{
    private SensorReadingTypeFactoryInterface $sensorReadingTypeFactory;

    private SensorTypeFactoryInterface $sensorTypeFactory;

    private array $serverErrors = [];

    private array $userInputErrors = [];

    public function __construct(
        SensorReadingTypeFactoryInterface $sensorReadingTypeFactory,
        SensorTypeFactoryInterface $sensorTypeFactory,
    ) {
        $this->sensorReadingTypeFactory = $sensorReadingTypeFactory;
        $this->sensorTypeFactory = $sensorTypeFactory;
    }

    public function handleSensorReadingTypeCreation(Sensors $sensor): void
    {
        try {
            $this->createNewSensorReadingTypeData($sensor);
        } catch (BadRequestException $exception) {
            $this->userInputErrors[] = $exception->getMessage();
        } catch (ORMException | SensorTypeException $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = $e->getMessage();
        }
    }

    private function createNewSensorReadingTypeData(Sensors $sensor)
    {
        $dateTimeNow = new DateTime();

        foreach (SensorType::ALL_SENSOR_TYPE_DATA as $sensorNames => $sensorTypeData) {
            if ($sensorNames === $sensor->getSensorTypeID()->getSensorType()) {
                $newSensorTypeObject = new $sensorTypeData['object'];
                if ($newSensorTypeObject instanceof SensorInterface) {
                    $newSensorTypeObject->setSensorObject($sensor);
                    $sensorTypeRepository = $this->sensorTypeFactory->getSensorTypeRepository($sensorTypeData['object']);
                    foreach ($sensorTypeData['readingTypes'] as $readingType => $readingTypeObject) {
                        $newReadingTypeObject = new $readingTypeObject;

                        $sensorReadingRepository = $this->sensorReadingTypeFactory->getSensorReadingTypeRepository($readingTypeObject);

                        if ($newReadingTypeObject instanceof Temperature) {
                            $newSensorTypeObject->setTempObject($newReadingTypeObject);
                        }
                        if ($newReadingTypeObject instanceof Humidity) {
                            $newSensorTypeObject->setHumidObject($newReadingTypeObject);
                        }
                        if ($newReadingTypeObject instanceof Analog) {
                            $newSensorTypeObject->setAnalogObject($newReadingTypeObject);
                        }
                        if ($newReadingTypeObject instanceof Latitude) {
                            $newSensorTypeObject->setLatitudeObject($newReadingTypeObject);
                        }

                        if ($newReadingTypeObject instanceof StandardReadingSensorInterface) {
                            $newReadingTypeObject->setSensorNameID($sensor);
                            $newReadingTypeObject->setCurrentReading(10);
                            $newReadingTypeObject->setTime(clone $dateTimeNow);

                            $sensorReadingRepository->persist($newReadingTypeObject);
                        }
                    }
                    $sensorTypeRepository->persist($newSensorTypeObject);
                    $sensorTypeRepository->flush();
                }
            }
        }
        if (empty($newSensorTypeObject)) {
            throw new SensorTypeException(
                sprintf(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED,
                    $sensor->getSensorTypeID()->getSensorType()
                )
            );
        }
    }

    public function getServerErrors(): array
    {
        return $this->serverErrors;
    }

    public function getUserInputErrors(): array
    {
        return $this->userInputErrors;
    }
}
