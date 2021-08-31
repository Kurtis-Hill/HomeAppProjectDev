<?php

namespace App\Services\ESPDeviceSensor\SensorData;

use App\Entity\Devices\Devices;
use App\Entity\Sensors\ConstantRecording\ConstAnalog;
use App\Entity\Sensors\ConstantRecording\ConstHumid;
use App\Entity\Sensors\ConstantRecording\ConstTemp;
use App\Entity\Sensors\OutOfRangeRecordings\OutOfRangeAnalog;
use App\Entity\Sensors\OutOfRangeRecordings\OutofRangeHumid;
use App\Entity\Sensors\OutOfRangeRecordings\OutOfRangeTemp;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\SensorType;
use App\Entity\Sensors\SensorTypes\Dallas;
use App\Exceptions\DeviceNotFoundException;
use App\Exceptions\SensorNotFoundException;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorInterface;
use Doctrine\ORM\ORMException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use UnexpectedValueException;


class SensorDeviceDataQueueConsumerService extends AbstractSensorService
{
    /**
     * @param array $sensorData
     * @param Devices $device
     * @return bool
     */
        public function handleUpdateCurrentReadingSensorData(array $sensorData, Devices $device): bool
        {
            try {
                match ($sensorData['sensorType']) {
                    SensorType::DHT_SENSOR => $this->handleDallasUpdateRequest($sensorData, $device),
                    default => throw new UnexpectedValueException('No type has been added to handle this request')
                };

                return true;
            } catch (
                BadRequestException
                | SensorNotFoundException
                | DeviceNotFoundException
                | UnexpectedValueException $exception
            ) {
                $this->userInputErrors[] = $exception->getMessage();
            } catch (ORMException | \Exception $exception) {
                $this->serverErrors[] = $exception->getMessage();
            }

            return false;
        }

    /**
     * @param array $sensorData
     * @param Devices $device
     * @throws DeviceNotFoundException
     * @throws SensorNotFoundException
     */
    private function handleDallasUpdateRequest(array $sensorData, Devices $device): void
    {
        $dallasSensor = $this->em->getRepository(Dallas::class)->findSensorBySensorName($sensorData['sensorName'], $device);

        if ($dallasSensor === null) {
            throw new SensorNotFoundException(
                sprintf(
                    SensorNotFoundException::SENSOR_NOT_FOUND_WITH_SENSOR_NAME,
                    $sensorData['sensorName']
                )
            );
        }

        if (!$dallasSensor instanceof Dallas && $dallasSensor instanceof SensorInterface) {
            throw new DeviceNotFoundException(
                sprintf(
                    DeviceNotFoundException::DEVICE_NOT_FOUND_MESSAGE_WITH_SENSOR_NAME_AND_DEVICE_ID,
                    $dallasSensor->getSensorObject()->getSensorName(),
                    $device->getDeviceNameID()
                )
            );
        }

        $sensorFormData = $this->prepareSensorFormData(
            $dallasSensor->getSensorObject()->getSensorTypeID(),
            $sensorData['currentReading'],
            SensorType::UPDATE_CURRENT_READING_FORM_ARRAY_KEY
        );

        if (empty($sensorFormData)) {
            throw new RuntimeException(
                'Sensor form has failed to process correctly for sensor ' . $sensorData['sensorName']
            );
        }

        $this->processSensorForm(
            $sensorFormData,
            [$dallasSensor->getSensorObject()->getSensorTypeID()]
        );

        $this->checkAndProcessConstRecord($dallasSensor->getTempObject());

        $this->checkTemperatureOutOfBounds($dallasSensor);

        $this->em->persist($dallasSensor);
        $this->em->flush();
    }

    /**
     * @param AllSensorReadingTypeInterface $readingType
     */
    private function checkTemperatureOutOfBounds(AllSensorReadingTypeInterface $readingType): void
    {
        if ($readingType->isReadingOutOfBounds() === true) {
            if ($readingType instanceof Temperature) {
                $outOfBounds = new OutOfRangeTemp();
            }
            if ($readingType instanceof Humidity) {
                $outOfBounds = new OutofRangeHumid();
            }
            if ($readingType instanceof Latitude) {
                //@Todo make table
            }
            if ($readingType instanceof Analog) {
                $outOfBounds = new OutOfRangeAnalog();
            }

            if (!isset($outOfBounds)) {
                throw new BadRequestException(
                    'out of range table cannot be found you may need to update your application'
                );
            }

            $outOfBounds->setSensorID($readingType);
            $outOfBounds->setSensorReading($readingType->getCurrentReading());
            $outOfBounds->setTime();

            $this->em->persist($outOfBounds);
        }
    }

    /**
     * @param AllSensorReadingTypeInterface $readingType
     * @return void
     */
    private function checkAndProcessConstRecord(AllSensorReadingTypeInterface $readingType): void
    {
        if ($readingType->getConstRecord() !== false) {
            if ($readingType instanceof Temperature) {
                $constObject = new ConstTemp();
                $constObject->setSensorID($readingType);
            }
            if ($readingType instanceof Humidity) {
                $constObject = new ConstHumid();
                $constObject->setSensorID($readingType);
            }
            if ($readingType instanceof Latitude) {
                //to do make table
            }
            if ($readingType instanceof Analog) {
                $constObject = new ConstAnalog();
                $constObject->setSensorID($readingType);
            }

            $isCallable = [$readingType, 'setSensorID'];

            if (!isset($constObject) || !is_callable($isCallable)) throw new RuntimeException(
                'Sensor type: ' . $readingType->getSensorTypeName() . 'not currently suppoerted for constant recoding values'
            );

            $constObject->setSensorReading($readingType->getCurrentReading());
            $constObject->setTime();

            $this->em->persist($constObject);
        }
    }

    private function handleBmpUpdateRequest(Request $request)
    {

    }

    private function handleSoilUpdateRequest(Request $request)
    {

    }

    private function handleDhtUpdateRequest(Request $request)
    {

    }
}
