<?php


namespace App\ESPDeviceSensor\SensorDataServices;


use App\Entity\Devices\Devices;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\AbstractSensorUpdateService;
use App\Form\FormMessages;
use App\Form\SensorForms\AddNewSensorForm;
use App\HomeAppSensorCore\Interfaces\APIErrorInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use UnexpectedValueException;

class SensorUserDataUpdateService extends AbstractSensorUpdateService implements APIErrorInterface
{
    /**
     * @var array
     */
    private array $userInputErrors = [];

    /**
     * @var array
     */
    private array $serverErrors = [];

    /**
     * @param array $sensorData
     * @return Sensors|null
     */
    public function createNewSensor(array $sensorData): ?Sensors
    {
        try {
            $newSensor = new Sensors();

            $addNewSensorForm = $this->formFactory->create(AddNewSensorForm::class, $newSensor);

            $handledForm = $this->processNewSensorForm($addNewSensorForm, $sensorData);

            if ($handledForm === true) {
                $this->em->persist($addNewSensorForm->getData());
            }

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

        foreach (SensorType::ALL_SENSOR_TYPE_DATA as $sensorNames => $sensorTypeData) {
            if ($sensorNames === $sensor->getSensorTypeID()->getSensorType()) {
                $newSensorTypeObject = new $sensorTypeData['object'];
                if ($newSensorTypeObject instanceof StandardSensorTypeInterface) {
                    $newSensorTypeObject->setSensorObject($sensor);
                    foreach ($sensorTypeData['readingTypes'] as $readingType => $readingTypeObject) {
                        $newReadingTypeObject = new $readingTypeObject;

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

                            $this->em->persist($newReadingTypeObject);
                        }
                    }
                    $this->em->persist($newSensorTypeObject);
                }

                return $newSensorTypeObject;
            }
        }
        if (empty($newSensorTypeObject) || !$this->em->contains($newSensorTypeObject)) {
            if (!empty($newReadingTypeObject)) {
                $this->em->remove($newReadingTypeObject);
            }
            $this->em->remove($sensor);
            $this->em->flush();

            throw new BadRequestException('Sensor Type Not Recognised Your App May Need Updating');
        }

        return null;
    }

//    /**
//     * @param Devices $device
//     * @param string $sensorName
//     * @param array $updateData
//     */
//    public function handleSensorReadingBoundaryUpdate(Devices $device, string $sensorName, array $updateData): void
//    {
//        try {
//            $sensorTypeObjects = $this->em->getRepository(Sensors::class)->getSensorReadingTypeObjectsBySensorNameAndDevice($device, $sensorName, SensorType::SENSOR_READING_TYPE_DATA);
//            if (empty($sensorTypeObjects)) {
//                throw new UnexpectedValueException('No reading types were found for your request, please make sure your app is up to date');
//            }
//            $firstSensorTypeObject = $sensorTypeObjects[0];
//            $sensorType = $firstSensorTypeObject->getSensorObject()->getSensorTypeID();
//            $sensorFormData = $this->prepareSensorFormData($sensorType, $updateData, SensorType::OUT_OF_BOUND_FORM_ARRAY_KEY);
////dd($updateData, 'ho');
//            if (empty($sensorFormData)) {
//                throw new BadRequestException('something went wrong with processing the sensor reading update form');
//            }
//
//             $this->processSensorForm($sensorFormData, $sensorTypeObjects);
//        } catch (BadRequestException $exception) {
//            $this->userInputErrors[] = $exception->getMessage();
//        }catch (UnexpectedValueException $exception) {
//            $this->serverErrors[] = $exception->getMessage();
//        }
//    }

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
     * @return bool
     */
    private function processNewSensorForm(FormInterface $addNewSensorForm, array $sensorData): bool
    {
        $processedFormResult = $this->processForm($addNewSensorForm, $sensorData);

        $this->duplicateSensorOnSameDeviceCheck($addNewSensorForm->getData());

        return $processedFormResult;
    }


    #[Pure] public function getUserInputErrors(): array
    {
        return array_merge($this->getAllFormInputErrors(), $this->userInputErrors);
    }

    /**
     * @return array
     */
    public function getServerErrors(): array
    {
        return $this->serverErrors;
    }
}
