<?php


namespace App\Services\ESPDeviceSensor\SensorData;


use App\Entity\Core\User;
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
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\Entity\Sensors\SensorTypes\Dallas;
use App\HomeAppSensorCore\Interfaces\Core\APISensorUserInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;


class SensorDeviceDataService extends AbstractSensorService
{
    /**
     * @param Request $request
     * @return array|null
     */
    public function processSensorReadingUpdateRequest(Request $request): array
    {
        $sensorType = $request->request->get('sensor-type');

        if ($sensorType === SensorType::DHT_SENSOR) {
            $this->handleDhtUpdateRequest($request);
        }
        if ($sensorType === SensorType::DALLAS_TEMPERATURE) {
//            dd('no');
            $this->handleDallasUpdateRequest($request);
        }
        if ($sensorType === SensorType::BMP_SENSOR) {
            $this->handleBmpUpdateRequest($request);
        }
        if ($sensorType === SensorType::SOIL_SENSOR) {
            $this->handleSoilUpdateRequest($request);
        }

//        } catch (BadRequestException $exception) {
//            $this->userInputErrors[] = $exception->getMessage();
//        } catch (\RuntimeException $exception) {
//            $this->userInputErrors[] = 0;
//        } catch (ORMException $exception) {
//            $this->serverErrors[] = $exception->getMessage();
//        } catch (\Exception $exception) {
//            $this->serverErrors[] = $exception->getMessage();
//        }

        return [];
    }

    protected function setServiceUserSession()
    {
        if ($this->getUser() instanceof Devices) {
            $this->sensorUser = $this->getUser();
        }
        else {
            throw new \InvalidArgumentException('Wrong Entity Provided');
        }


    }

    /**
     * @param Request $request
     */
    private function handleDallasUpdateRequest(Request $request): void
    {
        $sensorReadings = $this->multipleSensorsWithSingularReading($request, Dallas::MAX_POSSIBLE_SENSORS);

        foreach ($sensorReadings as $sensorReading) {
        //     try {
                $sensor = $this->findSensorForAPIRequestByUserAndSensorName($this->getUser(), $sensorReading['sensorName']);

                if ($sensor === null) {
                    throw new BadRequestException('sensor not recognised ' .$sensorReading['sensorName']);
                }
                if (empty($sensorReading['sensorReading'])) {
                    throw new BadRequestException('sensor reading is empty for' .$sensor->getSensorName() ?: $sensorReading['sensorName']);
                }

                $temperatureObject = $this->em->getRepository(Dallas::class)->findDallasSensorBySensor($sensor);

                if (!$temperatureObject instanceof Temperature) {
                    throw new BadRequestException('No temperature sensor can be found for '.$sensor->getSensorName());
                }

                $sensorFormData = $this->prepareSensorFormData(
                    $request,
                    $temperatureObject->getSensorObject()->getSensorTypeID(),
                    SensorType::UPDATE_CURRENT_READING_FORM_ARRAY_KEY,
                    ['currentReading' => $sensorReading['sensorReading']]
                );

                if (!empty($sensorFormData)) {
                    $this->processSensorForm($sensorFormData, [$temperatureObject]);
                }
                if (!empty($this->userInputErrors)) {
                    throw new BadRequestException('Sensor Data is not in line with the outlined specification');
                }

                $this->checkAndProcessConstRecord($temperatureObject, $sensor);

                $this->checkTemperatureOutOfBounds($temperatureObject, $sensor);

                $this->em->persist($temperatureObject);
                // here intentionally so if any sensor fails to persist other sensor data can still be processed
                $this->em->flush();
                dd('yeaaa');
//            } catch (BadRequestException $exception) {
//                $this->userInputErrors[] = $exception->getMessage();
//            } catch (\RuntimeException $exception) {
//                $this->userInputErrors[] = 0;
//            } catch (ORMException $exception) {
//                $this->serverErrors[] = $exception->getMessage();
//            } catch (\Exception $exception) {
//                $this->serverErrors[] = $exception->getMessage();
//            }
        }
    }

    /**
     * @param StandardReadingSensorInterface $readingTypeTypeObject
     * @param Sensors $sensor
     */
    private function checkTemperatureOutOfBounds(StandardReadingSensorInterface $readingTypeTypeObject, Sensors $sensor): void
    {
        if ($readingTypeTypeObject->isReadingOutOfBounds() === true) {
            if ($readingTypeTypeObject instanceof Temperature) {
                $outOfBounds = new OutOfRangeTemp();
            }
            if ($readingTypeTypeObject instanceof Humidity) {
                $outOfBounds = new OutofRangeHumid();
            }
            if ($readingTypeTypeObject instanceof Latitude) {
                //@Todo make table
            }
            if ($readingTypeTypeObject instanceof Analog) {
                $outOfBounds = new OutOfRangeAnalog();
            }

            if (!isset($outOfBounds)) {
                throw new BadRequestException('out of range table cannot be found you may need to update your application');
            }

            $outOfBounds->setSensorID($sensor);
            $outOfBounds->setSensorReading($readingTypeTypeObject->getCurrentReading());
            $outOfBounds->setTime();

            $this->em->persist($outOfBounds);
        }
    }

    /**
     * @param StandardReadingSensorInterface $readingTypeObject
     * @param Sensors $sensor
     */
    private function checkAndProcessConstRecord(StandardReadingSensorInterface $readingTypeObject, Sensors $sensor)
    {
        if ($readingTypeObject->getConstRecord() === true) {
            if ($readingTypeObject instanceof Temperature) {
                $constObject = new ConstTemp();
            }
            if ($readingTypeObject instanceof Humidity) {
                $constObject = new ConstHumid();
            }
            if ($readingTypeObject instanceof Latitude) {
                //to do make table
            }
            if ($readingTypeObject instanceof Analog) {
                $constObject = new ConstAnalog();
            }

            if (!isset($constObject)) {
                throw new BadRequestException('constantly record table cannot be found you may need to update your application');
            }

            $constObject->setSensorReading($readingTypeObject->getCurrentReading());
            $constObject->setSensorID($sensor);
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

    /**
     * @param APISensorUserInterface $device
     * @param string $sensorName
     * @return Sensors|null
     */
    private function findSensorForAPIRequestByUserAndSensorName(APISensorUserInterface $device, string $sensorName): ?Sensors
    {
//        dd($device, $sensorName);
        $sensor = $this->em->getRepository(Sensors::class)->findOneBy(
            [
                'sensorName' => $sensorName,
                'deviceNameID' => $device
            ]
        );
dd($sensor);
        if (!$sensor instanceof Sensors) {
            throw new BadRequestException('no sensor named ' .$sensorName. ' exists');
        }

        return $sensor;
    }


    /**
     * Methods that handle different variety of sensor update requests
     */

    /**
     * @param Request $request
     * @param $maxPossibleReadings
     * @return array
     */
    private function multipleSensorsWithSingularReading(Request $request, $maxPossibleReadings): array
    {
        $sensorUpdateDetails = [];

        for ($i = 1; $i < $maxPossibleReadings; ++$i) {
            $sensorName = $request->request->get('sensor-name'.$i);
            if (!empty($sensorName)) {
                $sensorReading = $request->request->get('sensor-reading'.$i);

                $sensorUpdateDetails[] = [
                    'sensorName' => $sensorName,
                    'sensorReading' => $sensorReading
                ];
            } else {
                break;
            }
        }

        return $sensorUpdateDetails;
    }
}
