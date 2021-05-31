<?php


namespace App\Tests\Controller\UserInterface;


use App\API\HTTPStatusCodes;
use App\Controller\Core\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Entity\Card\CardColour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\CardView;
use App\Entity\Card\Icons;
use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\User;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\Entity\Sensors\SensorTypes\Bmp;
use App\HomeAppSensorCore\ESPDeviceSensor\AbstractHomeAppUserSensorServiceCore;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use function PHPUnit\Framework\assertStringContainsString;


class CardDataControllerTest extends WebTestCase
{
    private const API_CARD_DATA_RETURN_CARD_DTO_ROUTE = '/HomeApp/api/card-data/cards';

    private const API_CARD_VIEW_FORM_DTO_URL = '/HomeApp/api/card-data/card-state-view-form';

    private const API_UPDATE_CARD_VIEW_FORM = '/HomeApp/api/card-data/update-card-view';

    /**
     * @var KernelBrowser
     */
    private KernelBrowser $client;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var string|null
     */
    private ?string $userToken = null;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        try {
            $this->setUserToken();
        } catch (\JsonException $e) {
            error_log($e);
        }
    }

    /**
     * @return void
     * @throws \JsonException
     */
    private function setUserToken()
    {
        if ($this->userToken === null) {
            $this->client->request(
                'POST',
                SecurityController::API_USER_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"'.UserDataFixtures::ADMIN_USER.'","password":"'.UserDataFixtures::ADMIN_PASSWORD.'"}'
            );

            $requestResponse = $this->client->getResponse();
            $requestData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $this->userToken = $requestData['token'];
        }
    }

    //returnCardDataDTOs Tests
    public function test_returning_all_card_dto_index()
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $groupNameMappingEntities = $this->entityManager->getRepository(GroupnNameMapping::class)->getAllGroupMappingEntitiesForUser($testUser);

        $testUser->setUserGroupMappingEntities($groupNameMappingEntities);

        $countIndexCards = count($this->entityManager->getRepository(CardView::class)->getAllIndexSensorTypeObjectsForUser($testUser, AbstractHomeAppUserSensorServiceCore::SENSOR_TYPE_DATA));

        $this->client->request(
            'GET',
            self::API_CARD_DATA_RETURN_CARD_DTO_ROUTE,
            ['view' => 'index'],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        foreach ($responseData as $cardData) {
            $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $testUser, 'sensorName' => $cardData['sensorName']]);
            $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);

            foreach ($cardData['sensorData'] as $sensorData) {
                $readingTypeObject = $this->entityManager->getRepository('App\Entity\Sensors\ReadingTypes\\' . ucfirst($sensorData['sensorType']))->findOneBy(['sensorNameID' => $sensorObject]);

                if ($readingTypeObject instanceof StandardReadingSensorInterface) {
                    self::assertEquals($readingTypeObject->getHighReading(), $sensorData['highReading']);
                    self::assertEquals($readingTypeObject->getLowReading(), $sensorData['lowReading']);
                    self::assertEquals($readingTypeObject->getCurrentReading(), $sensorData['currentReading']);
                    self::assertEquals($readingTypeObject->getMeasurementDifferenceHighReading(), $sensorData['getCurrentHighDifference']);
                    self::assertEquals($readingTypeObject->getMeasurementDifferenceLowReading(), $sensorData['getCurrentLowDifference']);

                    if ($readingTypeObject instanceof Temperature) {
                        self::assertEquals(Temperature::READING_SYMBOL, $sensorData['readingSymbol']);
                    }
                    if ($readingTypeObject instanceof Humidity) {
                        self::assertEquals(Humidity::READING_SYMBOL, $sensorData['readingSymbol']);
                    }
                    if ($readingTypeObject instanceof Latitude) {
                        self::assertNull($sensorData['readingSymbol']);
                    }
                    if ($readingTypeObject instanceof Analog) {
                        self::assertNull($sensorData['readingSymbol']);
                    }
                }
            }

            self::assertEquals($sensorObject->getSensorName(), $cardData['sensorName'], 'sensor names did not match from the object and the dto response data');
            self::assertEquals($sensorObject->getSensorTypeID()->getSensorType(), $cardData['sensorType'], 'sensor types did not match from the object and the dto response data');
            self::assertEquals($sensorObject->getDeviceNameID()->getRoomObject()->getRoom(), $cardData['sensorRoom'], 'sensor room did not match from the object and the dto response data');
            self::assertEquals($cardViewObject->getCardIconID()->getIconName(), $cardData['cardIcon'], 'sensor card icons did not match from the object and the dto response data');
            self::assertEquals($cardViewObject->getCardColourID()->getColour(), $cardData['cardColour'], 'sensor card colour did not match from the object and the dto response data');
            self::assertEquals($cardViewObject->getCardViewID(), $cardData['cardViewID'], 'card view id\'s did not match from the object and the dto response data');
        }

        self::assertCount($countIndexCards, $responseData);
        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function test_returning_all_card_dto_by_device()
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $groupNameMappingEntities = $this->entityManager->getRepository(GroupnNameMapping::class)->getAllGroupMappingEntitiesForUser($testUser);

        $testUser->setUserGroupMappingEntities($groupNameMappingEntities);

        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomAdminGroup']['referenceName']]);

        $countDeviceCards = count($this->entityManager->getRepository(CardView::class)->getAllCardReadingsForDevice($testUser, AbstractHomeAppUserSensorServiceCore::SENSOR_TYPE_DATA, $device->getDeviceNameId()));

        $this->client->request(
            'GET',
            self::API_CARD_DATA_RETURN_CARD_DTO_ROUTE,
            ['view' => 'device', 'device-id' => $device->getDeviceNameID()],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        foreach ($responseData as $cardData) {
            $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $testUser, 'sensorName' => $cardData['sensorName']]);
            $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);

            foreach ($cardData['sensorData'] as $sensorData) {
                $readingTypeObject = $this->entityManager->getRepository('App\Entity\Sensors\ReadingTypes\\' . ucfirst($sensorData['sensorType']))->findOneBy(['sensorNameID' => $sensorObject]);

                if ($readingTypeObject instanceof StandardReadingSensorInterface) {
                    self::assertEquals($readingTypeObject->getHighReading(), $sensorData['highReading']);
                    self::assertEquals($readingTypeObject->getLowReading(), $sensorData['lowReading']);
                    self::assertEquals($readingTypeObject->getCurrentReading(), $sensorData['currentReading']);
                    self::assertEquals($readingTypeObject->getMeasurementDifferenceHighReading(), $sensorData['getCurrentHighDifference']);
                    self::assertEquals($readingTypeObject->getMeasurementDifferenceLowReading(), $sensorData['getCurrentLowDifference']);

                    if ($readingTypeObject instanceof Temperature) {
                        self::assertEquals(Temperature::READING_SYMBOL, $sensorData['readingSymbol']);
                    }
                    if ($readingTypeObject instanceof Humidity) {
                        self::assertEquals(Humidity::READING_SYMBOL, $sensorData['readingSymbol']);
                    }
                    if ($readingTypeObject instanceof Latitude) {
                        self::assertNull($sensorData['readingSymbol']);
                    }
                    if ($readingTypeObject instanceof Analog) {
                        self::assertNull($sensorData['readingSymbol']);
                    }
                }
            }

            self::assertEquals($sensorObject->getSensorName(), $cardData['sensorName'], 'sensor names did not match from the object and the dto response data');
            self::assertEquals($sensorObject->getSensorTypeID()->getSensorType(), $cardData['sensorType'], 'sensor types did not match from the object and the dto response data');
            self::assertEquals($sensorObject->getDeviceNameID()->getRoomObject()->getRoom(), $cardData['sensorRoom'], 'sensor room did not match from the object and the dto response data');
            self::assertEquals($cardViewObject->getCardIconID()->getIconName(), $cardData['cardIcon'], 'sensor card icons did not match from the object and the dto response data');
            self::assertEquals($cardViewObject->getCardColourID()->getColour(), $cardData['cardColour'], 'sensor card colour did not match from the object and the dto response data');
            self::assertEquals($cardViewObject->getCardViewID(), $cardData['cardViewID'], 'card view id\'s did not match from the object and the dto response data');
        }

        self::assertCount($countDeviceCards, $responseData);
        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function test_returning_all_card_dto_by_device_with_bad_device_id()
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $groupNameMappingEntities = $this->entityManager->getRepository(GroupnNameMapping::class)->getAllGroupMappingEntitiesForUser($testUser);

        $testUser->setUserGroupMappingEntities($groupNameMappingEntities);

        while (true) {
            $invalidDeviceId = random_int(0, 10000);
            $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceNameID' => $invalidDeviceId]);

            if ($device === null) {
                break;
            }
        }

        $this->client->request(
            'GET',
            self::API_CARD_DATA_RETURN_CARD_DTO_ROUTE,
            ['view' => 'device', 'device-id' => $invalidDeviceId],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    // showCardViewForm Controller Method
    public function test_card_form_user_options()
    {
        $cardView = $this->entityManager->getRepository(CardView::class)->findAll()[0];

        $numberOfIcons = $this->entityManager->getRepository(Icons::class)->countAllIcons();
        $numberOfColours = $this->entityManager->getRepository(CardColour::class)->countAllColours();
        $numberOfStates = $this->entityManager->getRepository(Cardstate::class)->countAllStates();

        $this->client->request(
            'GET',
            self::API_CARD_VIEW_FORM_DTO_URL,
            ['cardViewID' => $cardView->getCardViewID()],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertJson($this->client->getResponse()->getContent());
        self::assertCount($numberOfIcons, $responseData['userIconSelections']);
        self::assertCount($numberOfColours, $responseData['userColourSelections']);
        self::assertCount($numberOfStates, $responseData['userCardViewSelections']);

        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function test_cardview_form_dallas_sensor()
    {
        $sensorType = SensorType::DALLAS_TEMPERATURE;

        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $testUser, 'sensorTypeID' => $sensorTypeObject]);

        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);

        $this->client->request(
            'GET',
            self::API_CARD_VIEW_FORM_DTO_URL,
            ['cardViewID' => $cardView->getCardViewID()],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        foreach ($responseData['sensorData'] as $sensorData) {
            $readingTypeObject = $this->entityManager->getRepository('App\Entity\Sensors\ReadingTypes\\' . ucfirst($sensorData['sensorType']))->findOneBy(['sensorNameID' => $sensorObject]);

            if ($readingTypeObject instanceof StandardReadingSensorInterface) {
                self::assertEquals($readingTypeObject->getHighReading(), $sensorData['highReading']);
                self::assertEquals($readingTypeObject->getLowReading(), $sensorData['lowReading']);
                self::assertEquals($readingTypeObject->getConstRecord(), $sensorData['constRecord']);

                if ($readingTypeObject instanceof Temperature) {
                    self::assertEquals(Temperature::READING_SYMBOL, $sensorData['readingSymbol']);
                }
                if ($readingTypeObject instanceof Humidity) {
                    self::assertEquals(Humidity::READING_SYMBOL, $sensorData['readingSymbol']);
                }
                if ($readingTypeObject instanceof Latitude) {
                    self::assertNull($sensorData['readingSymbol']);
                }
                if ($readingTypeObject instanceof Analog) {
                    self::assertNull($sensorData['readingSymbol']);
                }
            }
        }

        self::assertEquals($cardView->getCardViewID(), $responseData['cardViewID']);
        self::assertEquals($cardView->getCardIconID()->getIconID(), $responseData['cardIcon']['iconID']);
        self::assertEquals($cardView->getCardColourID()->getColourID(), $responseData['cardColour']['colourID']);
        self::assertEquals($cardView->getCardStateID()->getCardstateID(), $responseData['currentViewState']['stateID']);

        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }


    public function test_cardview_form_bmp_sensor()
    {
        $sensorType = SensorType::BMP_SENSOR;

        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $testUser, 'sensorTypeID' => $sensorTypeObject]);

        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);

        $this->client->request(
            'GET',
            self::API_CARD_VIEW_FORM_DTO_URL,
            ['cardViewID' => $cardView->getCardViewID()],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        foreach ($responseData['sensorData'] as $sensorData) {
            $readingTypeObject = $this->entityManager->getRepository('App\Entity\Sensors\ReadingTypes\\' . ucfirst($sensorData['sensorType']))->findOneBy(['sensorNameID' => $sensorObject]);

            if ($readingTypeObject instanceof StandardReadingSensorInterface) {
                self::assertEquals($readingTypeObject->getHighReading(), $sensorData['highReading']);
                self::assertEquals($readingTypeObject->getLowReading(), $sensorData['lowReading']);
                self::assertEquals($readingTypeObject->getConstRecord(), $sensorData['constRecord']);

                if ($readingTypeObject instanceof Temperature) {
                    self::assertEquals(Temperature::READING_SYMBOL, $sensorData['readingSymbol']);
                }
                if ($readingTypeObject instanceof Humidity) {
                    self::assertEquals(Humidity::READING_SYMBOL, $sensorData['readingSymbol']);
                }
                if ($readingTypeObject instanceof Latitude) {
                    self::assertNull($sensorData['readingSymbol']);
                }
                if ($readingTypeObject instanceof Analog) {
                    self::assertNull($sensorData['readingSymbol']);
                }
            }
        }

        self::assertEquals($cardView->getCardViewID(), $responseData['cardViewID']);
        self::assertEquals($cardView->getCardIconID()->getIconID(), $responseData['cardIcon']['iconID']);
        self::assertEquals($cardView->getCardColourID()->getColourID(), $responseData['cardColour']['colourID']);
        self::assertEquals($cardView->getCardStateID()->getCardstateID(), $responseData['currentViewState']['stateID']);

        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function test_cardview_form_dht_sensor()
    {
        $sensorType = SensorType::DHT_SENSOR;

        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $testUser, 'sensorTypeID' => $sensorTypeObject]);

        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);

        $this->client->request(
            'GET',
            self::API_CARD_VIEW_FORM_DTO_URL,
            ['cardViewID' => $cardView->getCardViewID()],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        foreach ($responseData['sensorData'] as $sensorData) {
            $readingTypeObject = $this->entityManager->getRepository('App\Entity\Sensors\ReadingTypes\\' . ucfirst($sensorData['sensorType']))->findOneBy(['sensorNameID' => $sensorObject]);

            if ($readingTypeObject instanceof StandardReadingSensorInterface) {
                self::assertEquals($readingTypeObject->getHighReading(), $sensorData['highReading']);
                self::assertEquals($readingTypeObject->getLowReading(), $sensorData['lowReading']);
                self::assertEquals($readingTypeObject->getConstRecord(), $sensorData['constRecord']);

                if ($readingTypeObject instanceof Temperature) {
                    self::assertEquals(Temperature::READING_SYMBOL, $sensorData['readingSymbol']);
                }
                if ($readingTypeObject instanceof Humidity) {
                    self::assertEquals(Humidity::READING_SYMBOL, $sensorData['readingSymbol']);
                }
                if ($readingTypeObject instanceof Latitude) {
                    self::assertNull($sensorData['readingSymbol']);
                }
                if ($readingTypeObject instanceof Analog) {
                    self::assertNull($sensorData['readingSymbol']);
                }
            }
        }

        self::assertEquals($cardView->getCardViewID(), $responseData['cardViewID']);
        self::assertEquals($cardView->getCardIconID()->getIconID(), $responseData['cardIcon']['iconID']);
        self::assertEquals($cardView->getCardColourID()->getColourID(), $responseData['cardColour']['colourID']);
        self::assertEquals($cardView->getCardStateID()->getCardstateID(), $responseData['currentViewState']['stateID']);

        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function test_cardview_form_soil_sensor()
    {
        $sensorType = SensorType::SOIL_SENSOR;

        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $testUser, 'sensorTypeID' => $sensorTypeObject]);

        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);

        $this->client->request(
            'GET',
            self::API_CARD_VIEW_FORM_DTO_URL,
            ['cardViewID' => $cardView->getCardViewID()],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        foreach ($responseData['sensorData'] as $sensorData) {
            $readingTypeObject = $this->entityManager->getRepository('App\Entity\Sensors\ReadingTypes\\' . ucfirst($sensorData['sensorType']))->findOneBy(['sensorNameID' => $sensorObject]);

            if ($readingTypeObject instanceof StandardReadingSensorInterface) {
                self::assertEquals($readingTypeObject->getHighReading(), $sensorData['highReading']);
                self::assertEquals($readingTypeObject->getLowReading(), $sensorData['lowReading']);
                self::assertEquals($readingTypeObject->getConstRecord(), $sensorData['constRecord']);

                if ($readingTypeObject instanceof Temperature) {
                    self::assertEquals(Temperature::READING_SYMBOL, $sensorData['readingSymbol']);
                }
                if ($readingTypeObject instanceof Humidity) {
                    self::assertEquals(Humidity::READING_SYMBOL, $sensorData['readingSymbol']);
                }
                if ($readingTypeObject instanceof Latitude) {
                    self::assertNull($sensorData['readingSymbol']);
                }
                if ($readingTypeObject instanceof Analog) {
                    self::assertNull($sensorData['readingSymbol']);
                }
            }
        }

        self::assertEquals($cardView->getCardViewID(), $responseData['cardViewID']);
        self::assertEquals($cardView->getCardIconID()->getIconID(), $responseData['cardIcon']['iconID']);
        self::assertEquals($cardView->getCardColourID()->getColourID(), $responseData['cardColour']['colourID']);
        self::assertEquals($cardView->getCardStateID()->getCardstateID(), $responseData['currentViewState']['stateID']);

        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    //updateCardView Tests
    public function test_can_update_card_view_form_all_selections_bmp()
    {
        $sensorType = SensorType::BMP_SENSOR;

        $testUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $testUser, 'sensorTypeID' => $sensorTypeObject]);

        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);

        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();

        foreach ($cardColours as $colour) {
            $newColour = $colour->getColourID();

            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
                break;
            }
        }

        foreach ($cardIcons as $icon) {
            $newIcon = $icon->getIconID();

            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
                break;
            }
        }

        foreach ($cardStates as $state) {
            $newState = $state->getCardstateID();

            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
                break;
            }
        }

        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);
        $temperatureObject = $bmpSensor->getTempObject();
        $humidityObject = $bmpSensor->getHumidObject();
        $latitudeObject = $bmpSensor->getLatitudeObject();

        $formData = [
            'card-view-id' => $cardViewObject->getCardViewID(),
            'card-colour' => $newColour,
            'card-icon' => $newIcon,
            'card-view-state' => $newState,

            'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
            'temperature-low-reading' => $temperatureObject->getLowReading() + 1,
            'temperature-const-record' => true,

            'humidity-high-reading' => $humidityObject->getHighReading() + 1,
            'humidity-low-reading' => $humidityObject->getLowReading() + 1,
            'humidity-const-record' => true,

            'latitude-high-reading' => $latitudeObject->getHighReading() + 1,
            'latitude-low-reading' => $latitudeObject->getLowReading() + 1,
            'latitude-const-record' => true,
        ];

        $this->client->request(
            'POST',
            self::API_UPDATE_CARD_VIEW_FORM,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

//        dd($this->client->getResponse()->getContent());
        $bmpSensorAfter = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);

        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);

        $temperatureObjectAfter = $bmpSensorAfter->getTempObject();
        self::assertEquals($formData['temperature-high-reading'], $temperatureObjectAfter->getHighReading());
        self::assertEquals($formData['temperature-low-reading'], $temperatureObjectAfter->getLowReading());
        self::assertEquals($formData['temperature-const-record'], $temperatureObjectAfter->getConstRecord());

        $humidityObjectAfter = $bmpSensorAfter->getHumidObject();
        self::assertEquals($formData['humidity-high-reading'], $humidityObjectAfter->getHighReading());
        self::assertEquals($formData['humidity-low-reading'], $humidityObjectAfter->getLowReading());
        self::assertEquals($formData['humidity-const-record'], $humidityObjectAfter->getConstRecord());

        $latitudeObjectAfter = $bmpSensorAfter->getLatitudeObject();
        self::assertEquals($formData['latitude-high-reading'], $latitudeObjectAfter->getHighReading());
        self::assertEquals($formData['latitude-low-reading'], $latitudeObjectAfter->getLowReading());
        self::assertEquals($formData['latitude-const-record'], $latitudeObjectAfter->getConstRecord());

        self::assertEquals($formData['card-colour'], $cardViewObject->getCardColourID()->getColourID());
        self::assertEquals($formData['card-icon'], $cardViewObject->getCardIconID()->getIconID());
        self::assertEquals($formData['card-view-state'], $cardViewObject->getCardStateID()->getCardstateID());

        self::assertEquals(HTTPStatusCodes::HTTP_UPDATED_SUCCESSFULLY, $this->client->getResponse()->getStatusCode());
    }

    public function test_can_update_card_view_form_temperature_selections_outofrange_data_bmp()
    {
        $sensorType = SensorType::BMP_SENSOR;

        $testUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $testUser, 'sensorTypeID' => $sensorTypeObject]);

        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);

        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();

        foreach ($cardColours as $colour) {
            $newColour = $colour->getColourID();

            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
                break;
            }
        }

        foreach ($cardIcons as $icon) {
            $newIcon = $icon->getIconID();

            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
                break;
            }
        }

        foreach ($cardStates as $state) {
            $newState = $state->getCardstateID();

            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
                break;
            }
        }

        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);
        $humidityObject = $bmpSensor->getHumidObject();
        $latitudeObject = $bmpSensor->getLatitudeObject();

        $formData = [
            'card-view-id' => $cardViewObject->getCardViewID(),
            'card-colour' => $newColour,
            'card-icon' => $newIcon,
            'card-view-state' => $newState,

            'temperature-high-reading' => '150',
            'temperature-low-reading' => '-60',
            'temperature-const-record' => true,

            'humidity-high-reading' => $humidityObject->getLowReading() + 1,
            'humidity-low-reading' => $humidityObject->getHighReading() + 1,
            'humidity-const-record' => true,

            'latitude-high-reading' => $latitudeObject->getHighReading() + 1,
            'latitude-low-reading' => $latitudeObject->getLowReading() + 1,
            'latitude-const-record' => true,
        ];

        $this->client->request(
            'POST',
            self::API_UPDATE_CARD_VIEW_FORM,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);

        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertStringContainsString('Temperature for this sensor cannot be over 125°C you entered 150°C', $responseData['responseData'][0]);
        self::assertStringContainsString('Temperature for this sensor cannot be under -55°C you entered -60°C', $responseData['responseData'][1]);

        $temperatureObject = $bmpSensor->getTempObject();
        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());

        $humidityObject = $bmpSensor->getHumidObject();
        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());

        $latitudeObject = $bmpSensor->getLatitudeObject();
        self::assertNotEquals($formData['latitude-high-reading'], $latitudeObject->getHighReading());
        self::assertNotEquals($formData['latitude-low-reading'], $latitudeObject->getLowReading());
        self::assertNotEquals($formData['latitude-const-record'], $latitudeObject->getConstRecord());

        self::assertNotEquals($formData['card-colour'], $cardViewObject->getCardColourID()->getColourID());
        self::assertNotEquals($formData['card-icon'], $cardViewObject->getCardIconID()->getIconID());
        self::assertNotEquals($formData['card-view-state'], $cardViewObject->getCardStateID()->getCardstateID());
//dd($this->client->getResponse()->getContent());
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_can_update_card_view_form_humidity_selections_outofrange_data_bmp()
    {
        $sensorType = SensorType::BMP_SENSOR;

        $testUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $testUser, 'sensorTypeID' => $sensorTypeObject]);

        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);

        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();

        foreach ($cardColours as $colour) {
            $newColour = $colour->getColourID();

            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
                break;
            }
        }

        foreach ($cardIcons as $icon) {
            $newIcon = $icon->getIconID();

            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
                break;
            }
        }

        foreach ($cardStates as $state) {
            $newState = $state->getCardstateID();

            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
                break;
            }
        }

        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);
        $temperatureObject = $bmpSensor->getTempObject();
        $latitudeObject = $bmpSensor->getLatitudeObject();

        $formData = [
            'card-view-id' => $cardViewObject->getCardViewID(),
            'card-colour' => $newColour,
            'card-icon' => $newIcon,
            'card-view-state' => $newState,

            'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
            'temperature-low-reading' => $temperatureObject->getLowReading() + 1,
            'temperature-const-record' => true,

            'humidity-high-reading' => '110',
            'humidity-low-reading' => '-10',
            'humidity-const-record' => true,

            'latitude-high-reading' => $latitudeObject->getHighReading() + 1,
            'latitude-low-reading' => $latitudeObject->getLowReading() + 1,
            'latitude-const-record' => true,
        ];

        $this->client->request(
            'POST',
            self::API_UPDATE_CARD_VIEW_FORM,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);

        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//        dd($responseData);
        self::assertStringContainsString('Humidity for this sensor cannot be over 100 you entered 110%', $responseData['responseData'][0]);
        self::assertStringContainsString('Humidity for this sensor cannot be under 0 you entered -10%', $responseData['responseData'][1]);

        $temperatureObject = $bmpSensor->getTempObject();
        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());

        $humidityObject = $bmpSensor->getHumidObject();
        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());

        $latitudeObject = $bmpSensor->getLatitudeObject();
        self::assertNotEquals($formData['latitude-high-reading'], $latitudeObject->getHighReading());
        self::assertNotEquals($formData['latitude-low-reading'], $latitudeObject->getLowReading());
        self::assertNotEquals($formData['latitude-const-record'], $latitudeObject->getConstRecord());

        self::assertNotEquals($formData['card-colour'], $cardViewObject->getCardColourID()->getColourID());
        self::assertNotEquals($formData['card-icon'], $cardViewObject->getCardIconID()->getIconID());
        self::assertNotEquals($formData['card-view-state'], $cardViewObject->getCardStateID()->getCardstateID());

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_can_update_card_view_form_latitude_selections_outofrange_data_bmp()
    {
        $sensorType = SensorType::BMP_SENSOR;

        $testUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $testUser, 'sensorTypeID' => $sensorTypeObject]);

        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);

        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();

        foreach ($cardColours as $colour) {
            $newColour = $colour->getColourID();

            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
                break;
            }
        }

        foreach ($cardIcons as $icon) {
            $newIcon = $icon->getIconID();

            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
                break;
            }
        }

        foreach ($cardStates as $state) {
            $newState = $state->getCardstateID();

            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
                break;
            }
        }

        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);
        $temperatureObject = $bmpSensor->getTempObject();
        $humidityObject = $bmpSensor->getHumidObject();
//        $latitudeObject = $bmpSensor->getLatitudeObject();

        $formData = [
            'card-view-id' => $cardViewObject->getCardViewID(),
            'card-colour' => $newColour,
            'card-icon' => $newIcon,
            'card-view-state' => $newState,

            'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
            'temperature-low-reading' => $temperatureObject->getLowReading() + 1,
            'temperature-const-record' => true,

            'humidity-high-reading' => $humidityObject->getHighReading() + 1,
            'humidity-low-reading' => $humidityObject->getLowReading() + 1,
            'humidity-const-record' => true,

            'latitude-high-reading' => '100',
            'latitude-low-reading' => '-5',
            'latitude-const-record' => true,
        ];

        $this->client->request(
            'POST',
            self::API_UPDATE_CARD_VIEW_FORM,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);

        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//        dd($responseData);
        self::assertStringContainsString('The highest possible latitude is 90 you entered "100"', $responseData['responseData'][0]);
        self::assertStringContainsString('The lowest possible latitude is 0 you entered "-5"', $responseData['responseData'][1]);

        $temperatureObject = $bmpSensor->getTempObject();
        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());

        $humidityObject = $bmpSensor->getHumidObject();
        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());

        $latitudeObject = $bmpSensor->getLatitudeObject();
        self::assertNotEquals($formData['latitude-high-reading'], $latitudeObject->getHighReading());
        self::assertNotEquals($formData['latitude-low-reading'], $latitudeObject->getLowReading());
        self::assertNotEquals($formData['latitude-const-record'], $latitudeObject->getConstRecord());

        self::assertNotEquals($formData['card-colour'], $cardViewObject->getCardColourID()->getColourID());
        self::assertNotEquals($formData['card-icon'], $cardViewObject->getCardIconID()->getIconID());
        self::assertNotEquals($formData['card-view-state'], $cardViewObject->getCardStateID()->getCardstateID());

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }









    public function test_cannot_add_wrong_const_record_input()
    {
        $sensorType = SensorType::BMP_SENSOR;

        $testUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $testUser, 'sensorTypeID' => $sensorTypeObject]);

        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);

        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();

        foreach ($cardColours as $colour) {
            $newColour = $colour->getColourID();

            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
                break;
            }
        }

        foreach ($cardIcons as $icon) {
            $newIcon = $icon->getIconID();

            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
                break;
            }
        }

        foreach ($cardStates as $state) {
            $newState = $state->getCardstateID();

            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
                break;
            }
        }

        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);
        $temperatureObject = $bmpSensor->getTempObject();
        $humidityObject = $bmpSensor->getHumidObject();
        $latitudeObject = $bmpSensor->getLatitudeObject();

        $formData = [
            'card-view-id' => $cardViewObject->getCardViewID(),
            'card-colour' => '1',
            'card-icon' => $newIcon,
            'card-view-state' => $newState,

            'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
            'temperature-low-reading' => $temperatureObject->getLowReading() + 1,
            'temperature-const-record' => true,

            'humidity-high-reading' => $humidityObject->getHighReading() + 1,
            'humidity-low-reading' => $humidityObject->getLowReading() + 1,
            'humidity-const-record' => true,

            'latitude-high-reading' => $latitudeObject->getHighReading() + 1,
            'latitude-low-reading' => $latitudeObject->getLowReading() + 1,
            'latitude-const-record' => false,
        ];

        $this->client->request(
            'POST',
            self::API_UPDATE_CARD_VIEW_FORM,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);

        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $testUser, 'sensorNameID' => $sensorObject]);
dd($bmpSensor);
//        dd($this->client->getResponse()->getContent());

        $temperatureObject = $bmpSensor->getTempObject();
        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());

        $humidityObject = $bmpSensor->getHumidObject();
        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());

        $latitudeObject = $bmpSensor->getLatitudeObject();
        self::assertNotEquals($formData['latitude-high-reading'], $latitudeObject->getHighReading());
        self::assertNotEquals($formData['latitude-low-reading'], $latitudeObject->getLowReading());
        self::assertNotEquals($formData['latitude-const-record'], $latitudeObject->getConstRecord());

        self::assertNotEquals($formData['card-colour'], $cardViewObject->getCardColourID()->getColourID());
        self::assertNotEquals($formData['card-icon'], $cardViewObject->getCardIconID()->getIconID());
        self::assertNotEquals($formData['card-view-state'], $cardViewObject->getCardStateID()->getCardstateID());

//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }
}
