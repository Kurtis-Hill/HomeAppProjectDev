<?php


namespace App\DataFixtures\ESP8266;

use App\DataFixtures\Card\CardFixtures;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Card\CardView;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensors;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SensorFixtures extends Fixture implements OrderedFixtureInterface
{
    private const SENSORS = [
        'Dht' => 'AdminDHTSensor',
        'Dallas' => 'AdminDallasSensor',
        'Soil' => 'AdminSoilSensor',
        'Bmp' => 'AdminBmpSensor'
    ];

    private const CARD_VIEW_CHECK = [
        'SensorOneON',
        'SensorTwoOFF',
        'SensorThreeINDEXONLY',
        'SensorFourROOMONLY,'
    ];

    public function getOrder()
    {
        return 5;
    }

    public function load(ObjectManager $manager): void
    {
        //need to add selection for front end card view checks
        $amountOfColours = count(CardFixtures::COLOURS) - 1;
        $amountOfIcons = count(CardFixtures::ICONS) - 1;

        $sensorCount = 0;
        foreach (SensorType::ALL_SENSOR_TYPE_DATA as $sensorType => $sensorData) {
            $sensor = new Sensors();
            $sensor->setDeviceNameID($this->getReference(ESP8266DeviceFixtures::ADMIN_TEST_DEVICE));
            $sensor->setSensorName(self::CARD_VIEW_CHECK['SensorOneON']."0".$sensorCount);
            $sensor->setSensorTypeID($this->getReference($sensorType));
            $sensor->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));

            $sensorUser = new Sensors();
            $sensor->setDeviceNameID($this->getReference(ESP8266DeviceFixtures::ADMIN_TEST_DEVICE));
            $sensor->setSensorName(self::CARD_VIEW_CHECK['SensorOneON']."1".$sensorCount);
            $sensor->setSensorTypeID($this->getReference($sensorType));
            $sensor->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));

            $manager->persist($sensor);

//                Card for the admin user
            $newCard = new CardView();
            $newCard->setSensorNameID($sensor);
            $newCard->setUserID($this->getReference(UserDataFixtures::ADMIN_USER));
            $newCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES['on']));
            $newCard->setCardColourID($this->getReference(CardFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour']));
            $newCard->setCardIconID($this->getReference(CardFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name']));

            $manager->persist($newCard);

            $newCard = new CardView();
            $newCard->setSensorNameID($sensor);
            $newCard->setUserID($this->getReference(UserDataFixtures::ADMIN_USER));
            $newCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES['on']));
            $newCard->setCardColourID($this->getReference(CardFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour']));
            $newCard->setCardIconID($this->getReference(CardFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name']));

            $manager->persist($newCard);
        }





        // sensor reading update sensor
        foreach (self::SENSORS as $sensorType => $name) {
            $newAdminSensor = new Sensors();
            $newAdminSensor->setDeviceNameID($this->getReference(ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName']));
            $newAdminSensor->setSensorName($name);
            $newAdminSensor->setSensorTypeID($this->getReference($sensorType));
            $newAdminSensor->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));

            $this->addReference($name, $newAdminSensor);
            $manager->persist($newAdminSensor);
        }
        // for permissions checks this wont cover every scenario down to a sensor by sensor but as there is currently no special permission
        $sensorCounter = 0;

        foreach (ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES as $device) {
            foreach (SensorType::ALL_SENSOR_TYPE_DATA as $sensorType => $sensorDetails) {
                $sensorNameAdminDevice = sprintf('%s%s%d', $sensorType, 'admin', $sensorCounter);
                $newAdminSensor = new Sensors();
                $newAdminSensor->setDeviceNameID($this->getReference($device['referenceName']));
                $newAdminSensor->setSensorName($sensorNameAdminDevice);
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
                $sensorNameUserDevice = sprintf('%s%s%d', $sensorType, 'user', $sensorCounter);
                $newRegularUserSensor = new Sensors();
                $newRegularUserSensor->setDeviceNameID($this->getReference($device['referenceName']));
                $newRegularUserSensor->setSensorName($sensorNameUserDevice);
                $newRegularUserSensor->setSensorTypeID($this->getReference($sensorType));
                $newRegularUserSensor->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));

                $manager->persist($newRegularUserSensor);

//                Card for the admin user
                $newCard = new CardView();
                $newCard->setSensorNameID($newRegularUserSensor);
                $newCard->setUserID($this->getReference(UserDataFixtures::ADMIN_USER));
                $newCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES['on']));
                $newCard->setCardColourID($this->getReference(CardFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour']));
                $newCard->setCardIconID($this->getReference(CardFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name']));

                $manager->persist($newCard);

//                Card for the regular user
                $otherUserCard = new CardView();
                $otherUserCard->setSensorNameID($newRegularUserSensor);
                $otherUserCard->setUserID($this->getReference(UserDataFixtures::REGULAR_USER));
                $otherUserCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES['on']));
                $otherUserCard->setCardColourID($this->getReference(CardFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour']));
                $otherUserCard->setCardIconID($this->getReference(CardFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name']));

                $manager->persist($otherUserCard);

                $newSensorType = new $sensorDetails['object'];
                $newSensorTypeTwo = new $sensorDetails['object'];

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
                    $newObjectTwo = new $object;
                    if ($newObjectTwo instanceof StandardReadingSensorInterface) {
                        $newObjectTwo->setSensorNameID($newAdminSensor);
                        $newObjectTwo->setCurrentReading(10);
                        $newObjectTwo->setTime();

                        if ($newSensorTypeTwo instanceof StandardSensorTypeInterface) {
                            $newSensorTypeTwo->setSensorObject($newAdminSensor);
                            if ($newSensorTypeTwo instanceof TemperatureSensorTypeInterface && $newObjectTwo instanceof Temperature) {
                                $newSensorTypeTwo->setTempObject($newObjectTwo);
                            }
                            if ($newSensorTypeTwo instanceof HumiditySensorTypeInterface && $newObjectTwo instanceof Humidity) {
                                $newSensorTypeTwo->setHumidObject($newObjectTwo);
                            }
                            if ($newSensorTypeTwo instanceof LatitudeSensorTypeInterface && $newObjectTwo instanceof Latitude) {
                                $newSensorTypeTwo->setLatitudeObject($newObjectTwo);
                            }
                            if ($newSensorTypeTwo instanceof AnalogSensorTypeInterface && $newObjectTwo instanceof Analog) {
                                $newSensorTypeTwo->setAnalogObject($newObjectTwo);
                            }
                        }

                        $manager->persist($newSensorTypeTwo);
                        $manager->persist($newObjectTwo);
                    }
                }
            }
            ++$sensorCounter;
        }

        $manager->flush();
    }

}
