<?php


namespace App\DataFixtures\ESP8266;



use App\DataFixtures\Card\CardFixtures;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Card\CardView;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\HomeAppSensorCore\ESPDeviceSensor\AbstractHomeAppUserSensorServiceCore;
use App\HomeAppSensorCore\Interfaces\SensorTypes\AnalogSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\HumiditySensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\LatitudeSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\TemperatureSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SensorFixtures extends Fixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 6;
    }

    public function load(ObjectManager $manager): void
    {
        // for permissions checks this wont cover every scenario down to a sensor by sensor but as there is currently no special permission
        // pending on the type of sensor a good selection of test data has been selected here
        $sensorCounter = 0;
        $amountOfColours = count(CardFixtures::COLOURS) - 1;
        $amountOfIcons = count(CardFixtures::ICONS) - 1;
        $amountOfState = count(CardFixtures::CARD_STATES) - 1;

        foreach (ESP8266DeviceFixtures::DEVICES as $device) {
            $extraTestData = $sensorCounter % 2 === 0;
            foreach (AbstractHomeAppUserSensorServiceCore::SENSOR_TYPE_DATA as $sensorType => $sensorDetails) {
                $newSensor = new Sensors();
                $newCard = new CardView();

//                dd(count(CardFixtures::CARD_STATES),
//count(CardFixtures::COLOURS));
                $newCard->setSensorNameID($newSensor);
                $newCard->setUserID($this->getReference($extraTestData ? UserDataFixtures::ADMIN_USER : UserDataFixtures::REGULAR_USER));
//                $newCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES[mt_rand(0, $amountOfState)]));
                $newCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES[0]));
                $newCard->setCardColourID($this->getReference(CardFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour']));
                $newCard->setCardIconID($this->getReference(CardFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name']));

                $manager->persist($newCard);
                if ($extraTestData) {
                    $otherUserCard = new CardView();
                    $otherUserCard->setSensorNameID($newSensor);
                    $otherUserCard->setUserID($this->getReference($extraTestData ? UserDataFixtures::REGULAR_USER : UserDataFixtures::ADMIN_USER));
//                    $otherUserCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES[mt_rand(0, $amountOfState)]));
                    $otherUserCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES[0]));
                    $otherUserCard->setCardColourID($this->getReference(CardFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour']));
                    $otherUserCard->setCardIconID($this->getReference(CardFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name']));

                    $manager->persist($otherUserCard);
                }


                $newSensor->setDeviceNameID($this->getReference($device['referenceName']));
                $newSensor->setSensorName($sensorType.$sensorCounter);
                $newSensor->setSensorTypeID($this->getReference($sensorType));
                $newSensor->setCreatedBy($this->getReference($extraTestData ? UserDataFixtures::ADMIN_USER : UserDataFixtures::REGULAR_USER));

                $this->addReference($sensorType.$sensorCounter, $newSensor);
                $manager->persist($newSensor);

                $newSensorType = new $sensorDetails['object'];
                foreach ($sensorDetails['readingTypes'] as $object) {
                    $newObject = new $object;
                    if ($newObject instanceof StandardReadingSensorInterface) {
                        $newObject->setSensorNameID($newSensor);
                        $newObject->setCurrentSensorReading(10);
                        $newObject->setTime();

                        if ($newSensorType instanceof StandardSensorTypeInterface) {
                            $newSensorType->setSensorObject($newSensor);
                            if ($newSensorType instanceof TemperatureSensorTypeInterface && $newObject instanceof Temperature) {
                                $newSensorType->setTempObject($newObject);
                            }
                            if ($newSensorType instanceof HumiditySensorTypeInterface && $newObject instanceof Humidity) {
                                $newSensorType->setHumidObject($newObject);
                            }
                            if ($newSensorType instanceof LatitudeSensorTypeInterface && $newObject instanceof Latitude) {
                                $newSensorType->setLatitudeObject($newObject);
                            }
                            if ($newSensorType instanceof AnalogSensorTypeInterface && $newObject instanceof Analog) {
                                $newSensorType->setAnalogObject($newObject);
                            }
                        }

                        $manager->persist($newSensorType);
                        $manager->persist($newObject);
                    }
                }
            }

            ++$sensorCounter;
        }

        $manager->flush();
    }

}
