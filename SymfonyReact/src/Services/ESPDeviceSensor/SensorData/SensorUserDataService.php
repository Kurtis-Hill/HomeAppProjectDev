<?php


namespace App\Services\ESPDeviceSensor\SensorData;


use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\Form\FormMessages;
use App\Form\SensorForms\AddNewSensorForm;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class SensorUserDataService extends AbstractSensorService
{
    /**
     * @param array $sensorData
     * @return Sensors|null
     */
    public function createNewSensor(array $sensorData): ?Sensors
    {
        try {
            $newSensor = new Sensors();

            $addNewSensorForm = $this->formFactory->create(AddNewSensorForm::class, $newSensor);

            $this->processNewSensorForm($addNewSensorForm, $sensorData);

            return $newSensor;
        } catch (BadRequestException $exception) {
            $this->userInputErrors[] = $exception->getMessage();
        } catch (ORMException $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = FormMessages::FORM_QUERY_ERROR;
        }

        return null;
    }

    /**
     * @param Sensors $sensor
     * @return StandardSensorTypeInterface|null
     */
    public function handleSensorReadingTypeCreation(Sensors $sensor): ?StandardSensorTypeInterface
    {
        try {
            return $this->createNewSensorReadingTypeData($sensor);
        } catch (BadRequestException $exception) {
            $this->userInputErrors[] = $exception->getMessage();
        } catch (ORMException $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = $e->getMessage();
        }

        $this->em->remove($sensor);
        $this->em->flush();

        return null;
    }

    /**
     * @param Sensors $sensor
     * @return StandardSensorTypeInterface|null
     */
    private function createNewSensorReadingTypeData(Sensors $sensor): ?StandardSensorTypeInterface
    {
        $dateTimeNow = new \DateTime();

        foreach (SensorType::SENSOR_TYPE_DATA as $sensorNames => $sensorTypeData) {
            if ($sensorNames === $sensor->getSensorTypeID()->getSensorType()) {
                $newSensorTypeObject = new $sensorTypeData['object'];
                if ($newSensorTypeObject instanceof StandardSensorTypeInterface) {
                    $newSensorTypeObject->setSensorObject($sensor);
                    foreach ($sensorTypeData['readingTypes'] as $readingType => $readingTypeObject) {
                        $newObject = new $readingTypeObject;

                        if ($newObject instanceof Temperature) {
                            $newSensorTypeObject->setTempObject($newObject);
                        }
                        if ($newObject instanceof Humidity) {
                            $newSensorTypeObject->setHumidObject($newObject);
                        }
                        if ($newObject instanceof Analog) {
                            $newSensorTypeObject->setAnalogObject($newObject);
                        }
                        if ($newObject instanceof Latitude) {
                            $newSensorTypeObject->setLatitudeObject($newObject);
                        }

                        if ($newObject instanceof StandardReadingSensorInterface) {
                            $newObject->setSensorNameID($sensor);
                            $newObject->setCurrentSensorReading(10);
                            $newObject->setTime(clone $dateTimeNow);

                            $this->em->persist($newObject);
                        }
                    }
                    $this->em->persist($newSensorTypeObject);
                }
//dd('d');
                return $newSensorTypeObject;
            }
        }
        if (empty($newSensorTypeObject) || !$this->em->contains($newSensorTypeObject)) {
            if (!empty($newObject)) {
                $this->em->remove($newObject);
            }
            $this->em->remove($sensor);
            $this->em->flush();

            throw new BadRequestException('Sensor Type Not Recognised Your App May Need Updating');
        }

        return null;
    }

    /**
     * @param Sensors $sensor
     * @param array $updateData
     *
     */
    public function handleSensorReadingBoundary(Sensors $sensor, array $updateData): void
    {
//        dd($updateData);
        try {
            $sensorTypeObject = $this->em->getRepository(Sensors::class)->getSensorCardFormDataBySensor($sensor, SensorType::SENSOR_READING_TYPE_DATA);
            if (empty($sensorTypeObject)) {
                throw new \UnexpectedValueException('No reading types were found for your request, please make sure your app is up to date');
            }
            $sensorFormData = $this->prepareSensorFormData($sensor->getSensorTypeID(), $updateData, SensorType::OUT_OF_BOUND_FORM_ARRAY_KEY);

            if (empty($sensorFormData)) {
                throw new BadRequestException('something went wrong with processing the sensor reading update form');
            }

             $this->processSensorForm($sensorFormData, $sensorTypeObject);
        } catch (BadRequestException $exception) {
            $this->userInputErrors[] = $exception->getMessage();
        }catch (\UnexpectedValueException $exception) {
            $this->serverErrors[] = $exception->getMessage();
        }
    }

    /**
     * @param Sensors $sensor
     */
    private function duplicateSensorOnSameDeviceCheck(Sensors $sensor): void
    {
        $currentUserSensorNameCheck = $this->em->getRepository(Sensors::class)->checkForDuplicateSensorOnDevice($sensor);

        if ($currentUserSensorNameCheck instanceof Sensors) {
            throw new BadRequestException('You already have a sensor named ' . $sensor->getSensorName());
        }
    }


    /**
     * @param FormInterface $addNewSensorForm
     * @param array $sensorData
     * @return void
     */
    private function processNewSensorForm(FormInterface $addNewSensorForm, array $sensorData): void
    {
        $this->processForm($addNewSensorForm, $this->em, $sensorData);

        $this->duplicateSensorOnSameDeviceCheck($addNewSensorForm->getData());
    }
}
