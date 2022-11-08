<?php

namespace App\Doctrine\DataFixtures\ESP8266;

use App\Doctrine\DataFixtures\Card\CardFixtures;
use App\Doctrine\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Soil;
use App\UserInterface\Entity\Card\CardView;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SensorFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURE_ORDER = 5;

    public const ALL_SENSOR_TYPE_DATA = [
        Dht::NAME => [
            'alias' => 'dht',
            'object' => Dht::class,
            'readingTypes' => [
                'temperature' =>  Temperature::class,
                'humidity' => Humidity::class,
            ],
        ],

        Dallas::NAME => [
            'alias' => 'dallas',
            'object' => Dallas::class,
            'readingTypes' => [
                Temperature::READING_TYPE =>  Temperature::class,
            ],
        ],

        Soil::NAME => [
            'alias' => 'soil',
            'object' => Soil::class,
            'readingTypes' => [
                Analog::READING_TYPE =>  Analog::class,
            ],
        ],

        Bmp::NAME => [
            'alias' => Bmp::NAME,
            'object' => Bmp::class,
            'readingTypes' => [
                'temperature' =>  Temperature::class,
                'humidity' => Humidity::class,
                'latitude' => Latitude::class,
            ],
        ],
    ];

    public const SENSORS = [
        Dht::NAME => 'AdminDHTSensor',
        Dallas::NAME => 'AdminDallasSensor',
        Soil::NAME => 'AdminSoilSensor',
        Bmp::NAME => 'AdminBmpSensor'
    ];

    private const CARD_VIEW_CHECK = [
        'on' => 'ON',
        'off' => 'OFF',
        'device' => 'DeviceONLY',
        'room' => 'RoomONLY',
    ];

    public function getOrder(): int
    {
        return self::FIXTURE_ORDER;
    }

    public function load(ObjectManager $manager): void
    {
        //need to add selection for front end card view checks
        $amountOfColours = count(CardFixtures::COLOURS) - 1;
        $amountOfIcons = count(CardFixtures::ICONS) - 1;

        // to check each sensor type with card states can be returned as to be displayed on the frontend
        $sensorCountCardView = 0;
        foreach (self::CARD_VIEW_CHECK as $state => $sensorData) {
            foreach (self::ALL_SENSOR_TYPE_DATA as $name => $sensorDetails) {
                $sensor = new Sensor();
                $sensor->setDeviceObject($this->getReference(ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName']));
                $sensor->setSensorName($sensorData.$name.$sensorCountCardView);
                $sensor->setSensorTypeID($this->getReference($name));
                $sensor->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));

                $manager->persist($sensor);

                $newCard = new CardView();
                $newCard->setSensorNameID($sensor);
                $newCard->setUserID($this->getReference(UserDataFixtures::ADMIN_USER));
                $newCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES[$state]));
                $newCard->setCardColourID($this->getReference(CardFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour']));
                $newCard->setCardIconID($this->getReference(CardFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name']));

                $manager->persist($newCard);
                $newSensorType = new $sensorDetails['object']();

                foreach ($sensorDetails['readingTypes'] as $object) {
                    $newObject = new $object();
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
        foreach (self::SENSORS as $sensorTypeFirst => $name) {
            $newAdminSensorTwo = new Sensor();
            $newAdminSensorTwo->setDeviceObject($this->getReference(ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName']));
            $newAdminSensorTwo->setSensorName($name);
            $newAdminSensorTwo->setSensorTypeID($this->getReference($sensorTypeFirst));
            $newAdminSensorTwo->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));

            $this->addReference($name, $newAdminSensorTwo);
            $manager->persist($newAdminSensorTwo);

            foreach (self::ALL_SENSOR_TYPE_DATA as $sensorType => $sensorDetails) {
                $newSensorType = new $sensorDetails['object']();
                if ($sensorTypeFirst === $sensorType) {
                    foreach ($sensorDetails['readingTypes'] as $object) {
                        $this->setSensorObjects($object, $newAdminSensorTwo, $newSensorType, $manager);
                    }
                }
            }
        }
        // for permissions checks one sensor of each type for each variation of device creation
        $sensorCounter = 0;

        foreach (ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES as $device) {
            foreach (self::ALL_SENSOR_TYPE_DATA as $sensorType => $sensorDetails) {
                $sensorNameAdminDevice = sprintf('%s%s%d', $sensorType, 'admin', $sensorCounter);
                $newAdminSensor = new Sensor();
                $newAdminSensor->setDeviceObject($this->getReference($device['referenceName']));
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
                $newRegularUserSensor = new Sensor();
                $newRegularUserSensor->setDeviceObject($this->getReference($device['referenceName']));
                $newRegularUserSensor->setSensorName($sensorNameUserDevice);
                $newRegularUserSensor->setSensorTypeID($this->getReference($sensorType));
                $newRegularUserSensor->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));

                $manager->persist($newRegularUserSensor);

//                CardView for the admin user
                $newCard = new CardView();
                $newCard->setSensorNameID($newRegularUserSensor);
                $newCard->setUserID($this->getReference(UserDataFixtures::ADMIN_USER));
                $newCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES['on']));
                $newCard->setCardColourID($this->getReference(CardFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour']));
                $newCard->setCardIconID($this->getReference(CardFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name']));

                $manager->persist($newCard);

//                CardView for the regular user
                $otherUserCard = new CardView();
                $otherUserCard->setSensorNameID($newRegularUserSensor);
                $otherUserCard->setUserID($this->getReference(UserDataFixtures::REGULAR_USER));
                $otherUserCard->setCardStateID($this->getReference(CardFixtures::CARD_STATES['on']));
                $otherUserCard->setCardColourID($this->getReference(CardFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour']));
                $otherUserCard->setCardIconID($this->getReference(CardFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name']));

                $manager->persist($otherUserCard);

                $newSensorType = new $sensorDetails['object']();
                $newSensorTypeTwo = new $sensorDetails['object']();

                foreach ($sensorDetails['readingTypes'] as $readingTypeObjects) {
                    $this->setSensorObjects($readingTypeObjects, $newAdminSensor, $newSensorType, $manager);
                    $this->setSensorObjects($readingTypeObjects, $newRegularUserSensor, $newSensorTypeTwo, $manager);
                }
            }
            ++$sensorCounter;
        }

        $manager->flush();
    }

    private function setSensorObjects(string $readingTypeObjects, Sensor $newAdminSensor, SensorTypeInterface $newSensorType, ObjectManager $manager): void
    {
        $newObject = new $readingTypeObjects();
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
    }
}
