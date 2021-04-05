<?php


namespace App\Services\ESPDeviceSensor\SensorData;

use App\Entity\Card\CardView;
use App\Entity\Core\GroupnNameMapping;
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
    /**
     * @var array
     */
    private array $userGroups;

    /**
     * SensorUserDataService constructor.
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Symfony\Component\Security\Core\Security $security
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     */
    public function __construct(EntityManagerInterface $em, Security $security, FormFactoryInterface $formFactory)
    {
        parent::__construct($em, $security, $formFactory);

        try {
            $this->setUserVariables($security);
        } catch (\Exception $exception) {
            error_log($exception->getMessage());
        }
    }

    /**
     * @param \App\Entity\Sensors\Sensors $sensor
     * @param \App\Entity\Card\CardView $cardView
     * @param array $sensorData
     */
    public function handleSensorCreation(Sensors $sensor, CardView $cardView, array $sensorData)
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Sensors\Sensors $sensorType
     * @param array $cardSensorReadingObject
     */
    public function handleSensorReadingBoundary(Request $request, Sensors $sensorType, array $cardSensorReadingObject)
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
    private function createNewSensorReadingTypeData(Sensors $sensor, CardView $cardView, array $sensorData)
    {
        $deviceObject = $this->em->getRepository(Devices::class)->findDeviceByIdAndGroupNameIds(['deviceNameID' => $sensorData['deviceNameID'], 'groupNameIDs' => $this->userGroups]);

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
    private function userInputDataCheck(Sensors $sensorData)
    {
        $currentUserSensorNameCheck = $this->em->getRepository(Sensors::class)->checkForDuplicateSensor($sensorData, $this->userGroups);

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
            $this->userInputErrors[] = $exception->getMessage();
        } catch (\Exception | ORMException $e) {
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

        $this->userInputDataCheck($addNewSensorForm->getData());

        if ($addNewSensorForm->isSubmitted() && $addNewSensorForm->isValid()) {
            $this->em->persist($addNewSensorForm->getData());
            $this->em->flush();

        } else {
            foreach ($addNewSensorForm->getErrors(true, true) as $error) {
                $this->userInputErrors[] = $error->getMessage();
            }
        }

        return $addNewSensorForm;
    }


    /**
     * @param \Symfony\Component\Security\Core\Security $security
     */
    protected function setUserVariables(Security $security): void
    {
        if (!$security->getUser() instanceof User) {
            throw new \InvalidArgumentException('Wrong Entity Provided');
        }

        $this->userGroups = $this->em->getRepository(GroupnNameMapping::class)->getGroupsForUser($security->getUser());
    }

}
