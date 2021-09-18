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
use App\Entity\Sensors\SensorType;
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
        return 5;
    }

    public function load(ObjectManager $manager): void
    {
        // for permissions checks this wont cover every scenario down to a sensor by sensor but as there is currently no special permission
        // pending on the type of sensor a good selection of test data has been selected here

        //In future to create a true test set the contents of this method will have to be duplicated
        $sensorCounter = 0;
        $amountOfColours = count(CardFixtures::COLOURS) - 1;
        $amountOfIcons = count(CardFixtures::ICONS) - 1;

        foreach (ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES as $device) {
            $addExtra = $sensorCounter & 1 ;
            foreach (SensorType::ALL_SENSOR_TYPE_DATA as $sensorType => $sensorDetails) {
                $newAdminSensor = new Sensors();
                $newAdminSensor->setDeviceNameID($this->getReference($device['referenceName']));
                $newAdminSensor->setSensorName($sensorType.$sensorCounter);
                $newAdminSensor->setSensorTypeID($this->getReference($sensorType));
                $newAdminSensor->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));

                $manager->persist($newAdminSensor);

//                Card for the admin user
                $newCard = new CardView();
                $newCard->setSensorNameID($newAdminSensor);
                $newCard->setUserID($this->getReference(UserDataFixtures::ADMIN_USER));
                $newCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES['on']));
                $newCard->setCardColourID($this->getReference(CardFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour']));
                $newCard->setCardIconID($this->getReference(CardFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name']));

                $manager->persist($newCard);

//                Card for the regular user
                $otherUserCard = new CardView();
                $otherUserCard->setSensorNameID($newAdminSensor);
                $otherUserCard->setUserID($this->getReference(UserDataFixtures::REGULAR_USER));
                $otherUserCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES['on']));
                $otherUserCard->setCardColourID($this->getReference(CardFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour']));
                $otherUserCard->setCardIconID($this->getReference(CardFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name']));

                $manager->persist($otherUserCard);

                // Regular created devices with card view set to off to reduce interface noise
                if ($addExtra) {
                    $sensorCounterExtra = $sensorCounter + 10;
                    $newRegularUserSensor = new Sensors();
                    $newRegularUserSensor->setDeviceNameID($this->getReference($device['referenceName']));
                    $newRegularUserSensor->setSensorName($sensorType.$sensorCounterExtra);
                    $newRegularUserSensor->setSensorTypeID($this->getReference($sensorType));
                    $newRegularUserSensor->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));

                    $manager->persist($newRegularUserSensor);

    //                Card for the admin user
                    $newCard = new CardView();
                    $newCard->setSensorNameID($newRegularUserSensor);
                    $newCard->setUserID($this->getReference(UserDataFixtures::ADMIN_USER));
                    $newCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES['off']));
                    $newCard->setCardColourID($this->getReference(CardFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour']));
                    $newCard->setCardIconID($this->getReference(CardFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name']));

                    $manager->persist($newCard);

    //                Card for the regular user
                    $otherUserCard = new CardView();
                    $otherUserCard->setSensorNameID($newRegularUserSensor);
                    $otherUserCard->setUserID($this->getReference(UserDataFixtures::REGULAR_USER));
                    $otherUserCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES['off']));
                    $otherUserCard->setCardColourID($this->getReference(CardFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour']));
                    $otherUserCard->setCardIconID($this->getReference(CardFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name']));
                }

                $manager->persist($otherUserCard);

                $newSensorType = new $sensorDetails['object'];
                foreach ($sensorDetails['readingTypes'] as $object) {
                    $newObject = new $object;
                    if ($newObject instanceof StandardReadingSensorInterface) {
                        $newObject->setSensorNameID($newAdminSensor);
                        $newObject->setCurrentReading(10);
                        $newObject->setTime();

                        if ($newSensorType instanceof StandardSensorTypeInterface) {
                            $newSensorType->setSensorObject($newAdminSensor);
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
            ++ $sensorCounter;
        }

        $manager->flush();
    }

}
