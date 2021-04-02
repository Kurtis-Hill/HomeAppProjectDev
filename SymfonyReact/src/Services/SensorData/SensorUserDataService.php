<?php


namespace App\Services\SensorData;

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
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use App\Traits\FormProcessorTrait;
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
//    use FormProcessorTrait;

    private array $groupNameIds;

    public function __construct(EntityManagerInterface $em, Security $security, FormFactoryInterface $formFactory)
    {
        parent::__construct($em, $security, $formFactory);

        try {
          //  $this->setUserVariables($security);
        } catch (\Exception $exception) {
            error_log($exception->getMessage());
        }
    }

    public function handleSensorCreation(Sensors $sensor, CardView $cardView, array $sensorData)
    {
        try {
            $this->createNewSensorReadingTypeData($sensor, $cardView, $sensorData);
        } catch (BadRequestException $exception) {
            $this->userInputErrors[] = $exception->getMessage();

        } catch (\Exception | ORMException $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = $e->getMessage();
        }
    }

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
        $deviceObject = $this->em->getRepository(Devices::class)->findOneBy(['deviceNameID' => $sensorData['deviceNameID']]);

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
        $currentUserSensorNameCheck = $this->em->getRepository(Sensors::class)->checkForDuplicateSensor($sensorData);

        if ($currentUserSensorNameCheck instanceof Sensors) {
            throw new BadRequestException('You already have a sensor named '. $sensorData->getSensorName());
        }
    }


    /**
     * @param FormInterface $addNewSensorForm
     * @param array $sensorData
     * @return FormInterface|null
     */
    public function handleNewSensorFormSubmission(FormInterface $addNewSensorForm, array $sensorData): FormInterface|null
    {
        try {
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
    private function processNewSensorForm(FormInterface $addNewSensorForm, array $sensorData)
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



        private function setUserVariables(Security $security)
    {
        if (!$security->getUser() instanceof User) {
            throw new \InvalidArgumentException('Wrong Entity Provided');
        }

        $this->groupNameDetails = $this->em->getRepository(GroupnNameMapping::class)->getGroupsForUser($security->getUser());
    }

}
