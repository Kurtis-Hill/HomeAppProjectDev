<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor\ReadingTypeCreation;

use App\Core\APIInterface\APIErrorInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingTypeFactoryInterface;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorType\SensorTypeFactoryInterface;
use DateTime;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ReadingTypeCreationService implements SensorReadingTypeCreationInterface
{
    private SensorReadingTypeFactoryInterface $sensorReadingTypeFactory;

    private SensorTypeFactoryInterface $sensorTypeFactory;

    public function __construct(
        SensorReadingTypeFactoryInterface $sensorReadingTypeFactory,
        SensorTypeFactoryInterface $sensorTypeFactory,
    ) {
        $this->sensorReadingTypeFactory = $sensorReadingTypeFactory;
        $this->sensorTypeFactory = $sensorTypeFactory;
    }

    public function handleSensorReadingTypeCreation(Sensor $sensor): array
    {
        $errors = [];
        try {
            $this->createNewSensorReadingTypeData($sensor);
        } catch (SensorTypeException $e) {
            $errors[] = $e->getMessage();
        } catch (ORMException $e) {
            $errors[] = 'Failed to save sensor reading types';
        }

        return $errors;
    }

    private function createNewSensorReadingTypeData(Sensor $sensor): void
    {
        $dateTimeNow = new DateTime();

        foreach (SensorType::ALL_SENSOR_TYPE_DATA as $sensorNames => $sensorTypeData) {
            if ($sensorNames === $sensor->getSensorTypeObject()->getSensorType()) {
                $newSensorTypeObject = new $sensorTypeData['object'];
                if ($newSensorTypeObject instanceof SensorTypeInterface) {
                    $newSensorTypeObject->setSensorObject($sensor);
                    $sensorTypeRepository = $this->sensorTypeFactory->getSensorTypeRepository($sensorTypeData['object']);
                    foreach ($sensorTypeData['readingTypes'] as $readingType => $readingTypeObject) {
                        $newReadingTypeObject = new $readingTypeObject;
                        $sensorReadingRepository = $this->sensorReadingTypeFactory->getSensorReadingTypeRepository($readingTypeObject);

                        //Adding New Sensor Type
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
                    $sensor->getSensorTypeObject()->getSensorType()
                )
            );
        }
    }
}
