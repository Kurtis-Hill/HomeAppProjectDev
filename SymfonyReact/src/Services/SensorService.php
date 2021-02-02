<?php


namespace App\Services;


use App\Entity\Card\CardColour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\CardView;
use App\Entity\Card\Icons;
use App\Entity\Sensors\Devices;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\HomeAppSensorCore\AbstractHomeAppSensorServiceCore;
use App\HomeAppSensorCore\Interfaces\SensorTypes\AnalogSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\HumiditySensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\LatitudeSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\TemperatureSensorTypeInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class SensorService extends AbstractHomeAppSensorServiceCore
{
    /**
     * @param FormInterface $addNewSensorForm
     * @param array $sensorData
     * @return FormInterface|null
     */
    public function handleNewSensorFormSubmission(FormInterface $addNewSensorForm, array $sensorData): ?FormInterface
    {
        try {
            $this->userInputDataCheck($sensorData);

//            $this->userInputErrors[] = 'No Device Recognised';

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

        if ($addNewSensorForm->isSubmitted() && $addNewSensorForm->isValid()) {
            $newSensorObject = $addNewSensorForm->getData();

            $this->em->persist($newSensorObject);
            $this->em->flush();

            $newSensorCard = $this->createNewSensorCard($newSensorObject);

            $this->createNewSensorType($newSensorObject, $newSensorCard, $sensorData);
        } else {
            foreach ($addNewSensorForm->getErrors(true, true) as $error) {
                $this->userInputErrors[] = $error->getMessage();
            }
        }

        return $addNewSensorForm;
    }

    /**
     * @param Sensors $sensorObject
     * @return CardView
     * @throws ORMException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function createNewSensorCard(Sensors $sensorObject): CardView
    {
        $maxIconNumer = $this->em->createQueryBuilder('icons')
            ->select('count(icons.iconID)')
            ->from(Icons::class, 'icons')
            ->getQuery()->getSingleScalarResult();

        $randomIcon = $this->em->getRepository(Icons::class)->findOneBy(['iconID' => random_int(1, $maxIconNumer)]);
        $randomColour = $this->em->getRepository(CardColour::class)->findOneBy(['colourID' => random_int(1, 4)]);
        $onCardState =  $this->em->getRepository(Cardstate::class)->findOneBy(['cardStateID' => Cardstate::ON]);

        $newCard = new CardView();
        $newCard->setSensorNameID($sensorObject);
        $newCard->setUserID($this->getUser());
        $newCard->setCardIconID($randomIcon);
        $newCard->setCardColourID($randomColour);
        $newCard->setCardStateID($onCardState);

        $this->em->persist($newCard);
        $this->em->flush();

        return $newCard;
    }

    private function createNewSensorType(Sensors $sensorType, CardView $cardView, array $sensorData)
    {
        $deviceObject = $this->em->getRepository(Devices::class)->findOneBy(['deviceNameID' => $sensorData['deviceNameID']]);

        if ($deviceObject instanceof Devices) {
            foreach (self::SENSOR_TYPE_DATA as $sensorNames => $sensorTypeData) {
                if ($sensorNames === $sensorType->getSensorTypeID()->getSensorType()) {
                    $newSensorTypeObject = new $sensorTypeData['object'];

                    $dateTimeNow = new \DateTime();

                    if ($newSensorTypeObject instanceof StandardSensorTypeInterface) {
                        $newSensorTypeObject->setCardViewObject($cardView);

                        if ($newSensorTypeObject instanceof TemperatureSensorTypeInterface) {
                            $newTempObject = new Temperature();

                            $newTempObject->setSensorNameID($sensorType);
                            $newTempObject->setDeviceNameID($deviceObject);
                            $newTempObject->setCurrentSensorReading(10);
                            $newTempObject->setTime(clone $dateTimeNow);

                            $this->em->persist($newTempObject);
                            $this->em->flush();

                            $newSensorTypeObject->setTempObject($newTempObject);
                        }
                        if ($newSensorTypeObject instanceof HumiditySensorTypeInterface) {
                            $newHumidObject = new Humidity();

                            $newHumidObject->setSensorNameID($sensorType);
                            $newHumidObject->setDeviceNameID($deviceObject);
                            $newHumidObject->setCurrentSensorReading(30);
                            $newHumidObject->setTime(clone $dateTimeNow);

                            $this->em->persist($newHumidObject);
                            $this->em->flush();

                            $newSensorTypeObject->setHumidObject($newHumidObject);
                        }
                        if ($newSensorTypeObject instanceof AnalogSensorTypeInterface) {
                            $newAnalogObject = new Analog();

                            $newAnalogObject->setSensorNameID($sensorType);
                            $newAnalogObject->setDeviceNameID($deviceObject);
                            $newAnalogObject->setCurrentSensorReading(30);
                            $newAnalogObject->setTime(clone $dateTimeNow);

                            $this->em->persist($newAnalogObject);
                            $this->em->flush();

                            $newSensorTypeObject->setAnalogObject($newAnalogObject);
                        }
                        if ($newSensorTypeObject instanceof LatitudeSensorTypeInterface) {
                            $newLatitudeObject = new Latitude();

                            $newLatitudeObject->setDeviceNameID($deviceObject);
                            $newLatitudeObject->setSensorNameID($sensorType);
                            $newLatitudeObject->setCurrentSensorReading(24.886436);
                            $newLatitudeObject->setTime(clone $dateTimeNow);

                            $this->em->persist($newLatitudeObject);
                            $this->em->flush();

                            $newSensorTypeObject->setLatitudeObject($newLatitudeObject);
                        }
                        $this->em->persist($newSensorTypeObject);
                        $this->em->flush();

                    }
                }
            }
        }
        else {
            throw new BadRequestException('No Device Recognised');
        }
    }


    /**
     * @param array $sensorData
     * //@TODO needs query adjustment to finy by one for specific group
     */
    private function userInputDataCheck(array $sensorData)
    {
       // throw new BadRequestException('You already have a sensor named '. $sensorData['sensorName']);
        $currentUserSensorNameCheck = $this->em->getRepository(Sensors::class)->findOneBy(['sensorName' => $sensorData['sensorName']]);

        if ($currentUserSensorNameCheck instanceof Sensors) {
            throw new BadRequestException('You already have a sensor named '. $sensorData['sensorName']);
        }
    }

    public function handleSensorDataUpdate()
    {

    }

    private function processSensorDataUpdate()
    {

    }

}
