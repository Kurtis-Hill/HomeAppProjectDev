<?php


namespace App\Services\ESPDeviceSensor\SensorData;

use App\Entity\Card\CardView;
use App\Entity\Core\User;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\Form\SensorForms\AddNewSensorForm;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class SensorUserDataService extends AbstractSensorService
{
    private User $sensorUser;
    /**
     * SensorUserDataService constructor.
     * @param EntityManagerInterface $em
     * @param Security $security
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManagerInterface $em, Security $security, FormFactoryInterface $formFactory)
    {
        parent::__construct($em, $security, $formFactory);

        try {
            $this->setServiceUserSession();
        } catch (\Exception $exception) {
            error_log($exception->getMessage());
        }
    }

    /**
     * @param Sensors $sensor
     * @param CardView $cardView
     * @param array $sensorData
     */
    public function handleSensorCreation(Sensors $sensor, CardView $cardView, array $sensorData): void
    {
        try {
            $this->createNewSensorReadingTypeData($sensor, $cardView, $sensorData);
        } catch (BadRequestException $exception) {
            $this->em->remove($sensor);
            $this->em->flush();
            $this->userInputErrors[] = $exception->getMessage();
        } catch (\Exception | ORMException $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @param Sensors $sensorType
     * @param array $cardSensorReadingObject
     */
    public function handleSensorReadingBoundary(Request $request, Sensors $sensorType, array $cardSensorReadingObject): void
    {
        try {
            $sensorFormData = $this->prepareSensorFormData($request, $sensorType->getSensorTypeID(), SensorType::OUT_OF_BOUND_FORM_ARRAY_KEY);

            if (!empty($this->userInputErrors)) {
                throw new BadRequestException();
            }
            if (!empty($sensorFormData)) {
                $this->processSensorForm($sensorFormData, $cardSensorReadingObject);
            }
        } catch (\RuntimeException $exception) {
            $this->serverErrors[] = 'Failed to process form data';
        }
    }

    /**
     * @param Sensors $sensor
     * @param CardView $cardView
     * @param array $sensorData
     */
    private function createNewSensorReadingTypeData(Sensors $sensor, CardView $cardView, array $sensorData): void
    {
        $deviceObject = $this->em->getRepository(Devices::class)->findDeviceByIdAndGroupNameIds(['deviceNameID' => $sensorData['deviceNameID'], 'groupNameIDs' => $this->getUser()->getUserGroupMappingEntities()]);
        if (!$deviceObject instanceof Devices) {
            $this->em->remove($sensor);
            $this->em->flush();

            throw new BadRequestException('No Device Recognised');
        }

        $dateTimeNow = new \DateTime();

        foreach (self::SENSOR_TYPE_DATA as $sensorNames => $sensorTypeData) {
            if ($sensorNames === $sensor->getSensorTypeID()->getSensorType()) {
                $newSensorTypeObject = new $sensorTypeData['object'];
                if ($newSensorTypeObject instanceof StandardSensorTypeInterface) {
                    $newSensorTypeObject->setCardViewObject($cardView);
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
                            $newObject->setDeviceNameID($deviceObject);
                            $newObject->setCurrentSensorReading(10);
                            $newObject->setTime(clone $dateTimeNow);

                            $this->em->persist($newObject);
                        }

                    }
                    $this->em->persist($newSensorTypeObject);
                }
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

        $this->em->flush();
    }

    /**
     * @param Sensors $sensorData
     */
    private function userInputDataCheck(Sensors $sensorData): void
    {
        $currentUserSensorNameCheck = $this->em->getRepository(Sensors::class)->checkForDuplicateSensor($sensorData, $this->getUser()->getUserGroupMappingEntities());

        if ($currentUserSensorNameCheck instanceof Sensors) {
            throw new BadRequestException('You already have a sensor named '. $sensorData->getSensorName());
        }
    }


    /**
     * @param array $sensorData
     * @return FormInterface|null
     */
    public function createNewSensor(array $sensorData): ?FormInterface
    {
        try {
            $newSensor = new Sensors();

            $addNewSensorForm = $this->formFactory->create(AddNewSensorForm::class, $newSensor);

            return $this->processNewSensorForm($addNewSensorForm, $sensorData);
        } catch (BadRequestException $exception) {
            $this->em->remove($addNewSensorForm->getData());
            $this->userInputErrors[] = $exception->getMessage();
        } catch (\Exception | ORMException $e) {
            $this->em->remove($addNewSensorForm->getData());
            error_log($e->getMessage());
            $this->serverErrors[] = $e->getMessage();
        }

        return null;
    }

    /**
     * @param FormInterface $addNewSensorForm
     * @param array $sensorData
     * @return FormInterface
     */
    private function processNewSensorForm(FormInterface $addNewSensorForm, array $sensorData): FormInterface
    {
        $addNewSensorForm->submit($sensorData);

        $device = $this->em->getRepository(Devices::class)->findDeviceByIdAndGroupNameIds(['deviceNameID' => $sensorData['deviceNameID'], 'groupNameIDs' => $this->getUser()->getGroupNameIds()]);
        $addNewSensorForm->getData()->setDeviceNameID($device);

        $this->userInputDataCheck($addNewSensorForm->getData());
        if ($addNewSensorForm->isSubmitted() && $addNewSensorForm->isValid()) {
            $this->em->persist($addNewSensorForm->getData());
            $this->em->flush();
        } else {
            foreach ($addNewSensorForm->getErrors(true, true) as $error) {
                $this->userInputErrors[] = $error->getMessage();
            }
        }
//dd('failed');
        return $addNewSensorForm;
    }


    /**
     * @param Security $security
     */
    protected function setServiceUserSession(): void
    {

        if ($this->getUser() instanceof User) {
            $this->sensorUser = $this->getUser();
            throw new \InvalidArgumentException('Wrong Entity Provided');
        }

        $this->setServiceUserSession($this->getUser());
    }

}
