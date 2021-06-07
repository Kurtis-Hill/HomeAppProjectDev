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
use App\HomeAppSensorCore\Interfaces\SensorTypes\AnalogSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\HumiditySensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\LatitudeSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\TemperatureSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

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
     * @param array $formData
     */
    public function handleSensorReadingBoundary(Sensors $sensor, array $formData): void
    {
        try {
//            dd($sensor, $formData);
            $sensorTypeObject = $this->em->getRepository(Sensors::class)->getSensorCardFormDataBySensor($sensor, SensorType::SENSOR_TYPE_DATA);
//            dd($sensorTypeObject);
            $sensorFormData = $this->prepareSensorFormData($sensor->getSensorTypeID(), $formData, SensorType::OUT_OF_BOUND_FORM_ARRAY_KEY);
//dd($sensorTypeObject, $sensorFormData, 'ba');
            if (!empty($this->userInputErrors) || empty($sensorFormData) || $sensorTypeObject === null) {

                throw new BadRequestException('something went wrong with processing the form');
            }
            if (!empty($sensorFormData)) {
                $this->processSensorForm($sensorFormData, $sensorTypeObject);
            }
        } catch (BadRequestException $exception) {
            $this->userInputErrors[] = 'Failed to process form data';
        }catch (\RuntimeException $exception) {
            $this->serverErrors[] = 'Failed to process form data';
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

    // update this method to include any other sensor types so that the requests can be prepared for processing
    public function processSensorUpdateRequestObject(Request $request, StandardSensorTypeInterface $sensorType): array
    {
        $formDataToProcess = [];

//        if ($sensorType instanceof TemperatureSensorTypeInterface) {
            $formDataToProcess[] = [
                'temperatureHighReading' => $request->get('temperature-high-reading'),
                'temperatureLowReading' => $request->get('temperature-low-reading'),
                'temperatureConstRecord' => $request->get('temperature-const-record'),

//        }
//        if ($sensorType instanceof  HumiditySensorTypeInterface) {
//            $formDataToProcess[] = [
                'humidityHighReading' => $request->get('humidity-high-reading'),
                'humidityLowReading' => $request->get('humidity-low-reading'),
                'humidityConstRecord' => $request->get('humidity-const-record'),
//            ];
//        }
//        if ($sensorType instanceof LatitudeSensorTypeInterface) {
//            $formDataToProcess[] = [
                'latitudeHighReading' => $request->get('latitude-high-reading'),
                'latitudeLowReading' => $request->get('latitude-low-reading'),
                'latitudeConstRecord' => $request->get('latitude-const-record'),
//            ];
//        }
//        if ($sensorType instanceof AnalogSensorTypeInterface) {
//            $formDataToProcess[] =  [
                'analogHighReading' => $request->get('analog-high-reading'),
                'analogLowReading' => $request->get('analog-low-reading'),
                'analogConstRecord' => $request->get('analog-const-record'),
            ];
//        }
        dd($formDataToProcess, 'g', $sensorType, $request->request->all());
        return $formDataToProcess;
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
