<?php


namespace App\DataFixtures\ESP8266;

use App\DataFixtures\Card\CardFixtures;
use App\DataFixtures\Core\UserDataFixtures;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\UserInterface\Entity\Card\CardView;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SensorFixtures extends Fixture implements OrderedFixtureInterface
{
    public const SENSORS = [
        Dht::NAME => 'AdminDHTSensor',
        Dallas::NAME => 'AdminDallasSensor',
        Soil::NAME => 'AdminSoilSensor',
        Bmp::NAME => 'AdminBmpSensor'
    ];

    private const CARD_VIEW_CHECK = [
        'on' => 'S1ON',
        'off' => 'S2OFF',
        'device' => 'S3DeviceONLY',
        'room' => 'S4ROOMONLY'
    ];

    public function getOrder(): int
    {
        return 5;
    }

    public function load(ObjectManager $manager): void
    {
        //need to add selection for front end card view checks
        $amountOfColours = count(CardFixtures::COLOURS) - 1;
        $amountOfIcons = count(CardFixtures::ICONS) - 1;

        // to check each sensor type with card states can be returned as to be displayed on the frontend
        $sensorCountCardView = 0;
        foreach (self::CARD_VIEW_CHECK as $state => $sensorData) {
            foreach (SensorType::ALL_SENSOR_TYPE_DATA as $name => $sensorDetails) {
                $sensor = new Sensor();
                $sensor->setDeviceObject($this->getReference(ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName']));
                $sensor->setSensorName($sensorData.$name.$sensorCountCardView);
                $sensor->setSensorType($this->getReference($name));
                $sensor->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));

                $manager->persist($sensor);

                $newCard = new CardView();
                $newCard->setSensorNameID($sensor);
                $newCard->setUserID($this->getReference(UserDataFixtures::ADMIN_USER));
                $newCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES[$state]));
                $newCard->setCardColourID($this->getReference(CardFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour']));
                $newCard->setCardIconID($this->getReference(CardFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name']));

                $manager->persist($newCard);
                $newSensorType = new $sensorDetails['object'];


                foreach ($sensorDetails['readingTypes'] as $object) {
                    $newObject = new $object;
                    if ($newObject instanceof StandardReadingSensorInterface) {
                        $newObject->setSensorObject($sensor);
                        $newObject->setCurrentReading($newObject instanceof Analog ? 1001 : 10);
                        $newObject->setUpdatedAt();

                        if ($newSensorType instanceof StandardSensorTypeInterface) {
                            $newSensorType->setSensorObject($sensor);
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
            ++$sensorCountCardView;
        }


        // sensor reading update sensor
        foreach (self::SENSORS as $sensorType => $name) {
            $newAdminSensor = new Sensor();
            $newAdminSensor->setDeviceObject($this->getReference(ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName']));
            $newAdminSensor->setSensorName($name);
            $newAdminSensor->setSensorType($this->getReference($sensorType));
            $newAdminSensor->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));

            $this->addReference($name, $newAdminSensor);
            $manager->persist($newAdminSensor);
        }
        // for permissions checks one sensor of each type for each variation of device creation
        $sensorCounter = 0;

        foreach (ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES as $device) {
            foreach (SensorType::ALL_SENSOR_TYPE_DATA as $sensorType => $sensorDetails) {
                $sensorNameAdminDevice = sprintf('%s%s%d', $sensorType, 'admin', $sensorCounter);
                $newAdminSensor = new Sensor();
                $newAdminSensor->setDeviceObject($this->getReference($device['referenceName']));
                $newAdminSensor->setSensorName($sensorNameAdminDevice);
                $newAdminSensor->setSensorType($this->getReference($sensorType));
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
                $newRegularUserSensor = new Sensor();
                $newRegularUserSensor->setDeviceObject($this->getReference($device['referenceName']));
                $newRegularUserSensor->setSensorName($sensorNameUserDevice);
                $newRegularUserSensor->setSensorType($this->getReference($sensorType));
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
                        $newObject->setSensorObject($newAdminSensor);
                        $newObject->setUpdatedAt();

                        if ($newSensorType instanceof StandardSensorTypeInterface) {
                            $newSensorType->setSensorObject($newAdminSensor);
                            if ($newSensorType instanceof TemperatureSensorTypeInterface && $newObject instanceof Temperature) {
                                $newObject->setCurrentReading(10);
                                $newSensorType->setTempObject($newObject);
                            }
                            if ($newSensorType instanceof HumiditySensorTypeInterface && $newObject instanceof Humidity) {
                                $newObject->setCurrentReading(10);
                                $newSensorType->setHumidObject($newObject);
                            }
                            if ($newSensorType instanceof LatitudeSensorTypeInterface && $newObject instanceof Latitude) {
                                $newObject->setCurrentReading(10);
                                $newSensorType->setLatitudeObject($newObject);
                            }
                            if ($newSensorType instanceof AnalogSensorTypeInterface && $newObject instanceof Analog) {
                                $newObject->setCurrentReading(1001);
                                $newSensorType->setAnalogObject($newObject);
                            }
                        }

                        $manager->persist($newSensorType);
                        $manager->persist($newObject);
                    }
                    $newObjectTwo = new $object;
                    if ($newObjectTwo instanceof StandardReadingSensorInterface) {
                        $newObjectTwo->setSensorObject($newRegularUserSensor);
                        $newObjectTwo->setUpdatedAt();

                        if ($newSensorTypeTwo instanceof StandardSensorTypeInterface) {
                            $newSensorTypeTwo->setSensorObject($newRegularUserSensor);
                            if ($newSensorTypeTwo instanceof TemperatureSensorTypeInterface && $newObjectTwo instanceof Temperature) {
                                $newObjectTwo->setCurrentReading(10);
                                $newSensorTypeTwo->setTempObject($newObjectTwo);
                            }
                            if ($newSensorTypeTwo instanceof HumiditySensorTypeInterface && $newObjectTwo instanceof Humidity) {
                                $newObjectTwo->setCurrentReading(10);
                                $newSensorTypeTwo->setHumidObject($newObjectTwo);
                            }
                            if ($newSensorTypeTwo instanceof LatitudeSensorTypeInterface && $newObjectTwo instanceof Latitude) {
                                $newObjectTwo->setCurrentReading(10);
                                $newSensorTypeTwo->setLatitudeObject($newObjectTwo);
                            }
                            if ($newSensorTypeTwo instanceof AnalogSensorTypeInterface && $newObjectTwo instanceof Analog) {
                                $newObjectTwo->setCurrentReading(1001);
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
