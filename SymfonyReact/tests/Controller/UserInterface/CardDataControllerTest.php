<?php


namespace App\Tests\Controller\UserInterface;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class CardDataControllerTest extends WebTestCase
{
//    private const API_CARD_DATA_RETURN_CARD_DTO_ROUTE = '/HomeApp/api/user/card-data/cards';
//
//    private const API_CARD_VIEW_FORM_DTO_URL = '/HomeApp/api/user/card-data/card-sensor-form';
//
//    private const API_UPDATE_CARD_VIEW_FORM = '/HomeApp/api/card-data/update-card-view';
//
//    /**
//     * @var KernelBrowser
//     */
//    private KernelBrowser $client;
//
//    /**
//     * @var EntityManagerInterface
//     */
//    private EntityManagerInterface $entityManager;
//
//    /**
//     * @var string|null
//     */
//    private ?string $userToken = null;
//
//    /**
//     * @var User|null
//     */
//    private ?User $testUser;
//
//
//    protected function setUp(): void
//    {
//        $this->client = static::createClient();
//
//        $this->entityManager = static::$kernel->getContainer()
//            ->get('doctrine')
//            ->getManager();
//
//        $userRepository = $this->entityManager->getRepository(User::class);
//        $this->testUser = $userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
////dd($this->testUser);
//        $groupNameMappingEntities = $this->entityManager->getRepository(GroupnNameMapping::class)->getAllGroupMappingEntitiesForUser($this->testUser);
//
//        $this->testUser->setUserGroupMappingEntities($groupNameMappingEntities);
//
//        try {
//            $this->setUserToken();
//        } catch (\JsonException $e) {
//            error_log($e);
//        }
//    }
//
//    /**
//     * @return void
//     * @throws \JsonException
//     */
//    private function setUserToken()
//    {
//        if ($this->userToken === null) {
//            $this->client->request(
//                'POST',
//                SecurityController::API_USER_LOGIN,
//                [],
//                [],
//                ['CONTENT_TYPE' => 'application/json'],
//                '{"username":"'.UserDataFixtures::ADMIN_USER.'","password":"'.UserDataFixtures::ADMIN_PASSWORD.'"}'
//            );
//
//            $requestResponse = $this->client->getResponse();
//            $requestData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);
//
//            $this->userToken = $requestData['token'];
//        }
//    }
//
//
//    //returnCardDataDTOs Tests
//    public function test_returning_all_card_dto_index()
//    {
//        $onCardState = $this->entityManager->getRepository(Cardstate::class)->findOneBy(['state' => Cardstate::ON]);
//
//        $countUsersCardsInOnState = count($this->entityManager->getRepository(CardView::class)->findBy(['cardStateID' => $onCardState, 'userID' => $this->testUser]));
//        $countIndexCards = count($this->entityManager->getRepository(CardView::class)->getAllSensorTypeObjectsForUser($this->testUser, SensorType::ALL_SENSOR_TYPE_DATA, Cardstate::INDEX_ONLY));
//        $fixtureCardCount = count(ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES) * count(SensorType::ALL_SENSOR_TYPE_DATA);
//
//        $this->client->request(
//            'GET',
//            self::API_CARD_DATA_RETURN_CARD_DTO_ROUTE,
//            ['view' => 'index'],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
////dd($responseData);
//        foreach ($responseData as $cardData) {
//            $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorName' => $cardData['sensorName']]);
//            $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
////if ($sensorObject === null) {
////    dd($cardData['sensorName']);
////}
//            foreach ($cardData['sensorData'] as $sensorData) {
//                $readingTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\ReadingTypes\\' . ucfirst($sensorData['sensorType']))->findOneBy(['sensorNameID' => $sensorObject]);
//
//                if ($readingTypeObject instanceof StandardReadingSensorInterface) {
//                    self::assertEquals($readingTypeObject->getHighReading(), $sensorData['highReading']);
//                    self::assertEquals($readingTypeObject->getLowReading(), $sensorData['lowReading']);
//                    self::assertEquals($readingTypeObject->getCurrentReading(), $sensorData['currentReading']);
//                    self::assertEquals($readingTypeObject->getMeasurementDifferenceHighReading(), $sensorData['getCurrentHighDifference']);
//                    self::assertEquals($readingTypeObject->getMeasurementDifferenceLowReading(), $sensorData['getCurrentLowDifference']);
//
//                    if ($readingTypeObject instanceof Temperature) {
//                        self::assertEquals(Temperature::READING_SYMBOL, $sensorData['readingSymbol']);
//                    }
//                    if ($readingTypeObject instanceof Humidity) {
//                        self::assertEquals(Humidity::READING_SYMBOL, $sensorData['readingSymbol']);
//                    }
//                    if ($readingTypeObject instanceof Latitude) {
//                        self::assertNull($sensorData['readingSymbol']);
//                    }
//                    if ($readingTypeObject instanceof Analog) {
//                        self::assertNull($sensorData['readingSymbol']);
//                    }
//                }
//            }
//
//            self::assertEquals($sensorObject->getSensorName(), $cardData['sensorName'], 'sensor names did not match from the object and the dto response data');
//            self::assertEquals($sensorObject->getSensorTypeID()->getSensorType(), $cardData['sensorType'], 'sensor types did not match from the object and the dto response data');
//            self::assertEquals($sensorObject->getDeviceNameID()->getRoomObject()->getRoom(), $cardData['sensorRoom'], 'sensor room did not match from the object and the dto response data');
//            self::assertEquals($cardViewObject->getCardIconID()->getIconName(), $cardData['cardIcon'], 'sensor card icons did not match from the object and the dto response data');
//            self::assertEquals($cardViewObject->getCardColourID()->getColour(), $cardData['cardColour'], 'sensor card colour did not match from the object and the dto response data');
//            self::assertEquals($cardViewObject->getCardViewID(), $cardData['cardViewID'], 'card view id\'s did not match from the object and the dto response data');
//        }
//
//        self::assertCount($fixtureCardCount, $responseData);
//        self::assertCount($countUsersCardsInOnState, $responseData);
//        self::assertCount($countIndexCards, $responseData);
//        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
//    }
//
//    public function test_returning_all_card_dto_by_device()
//    {
//        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomAdminGroup']['referenceName']]);
//        $countDeviceCards = count($this->entityManager->getRepository(CardView::class)->getAllCardReadingsForDevice($this->testUser, SensorType::ALL_SENSOR_TYPE_DATA, $device->getDeviceNameId()));
//
//        $this->client->request(
//            'GET',
//            self::API_CARD_DATA_RETURN_CARD_DTO_ROUTE,
//            ['view' => 'device', 'device-id' => $device->getDeviceNameID()],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
//
//        foreach ($responseData as $cardData) {
//            $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorName' => $cardData['sensorName']]);
//            $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//            foreach ($cardData['sensorData'] as $sensorData) {
//                $readingTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\ReadingTypes\\' . ucfirst($sensorData['sensorType']))->findOneBy(['sensorNameID' => $sensorObject]);
//
//                if ($readingTypeObject instanceof StandardReadingSensorInterface) {
//                    self::assertEquals($readingTypeObject->getHighReading(), $sensorData['highReading']);
//                    self::assertEquals($readingTypeObject->getLowReading(), $sensorData['lowReading']);
//                    self::assertEquals($readingTypeObject->getCurrentReading(), $sensorData['currentReading']);
//                    self::assertEquals($readingTypeObject->getMeasurementDifferenceHighReading(), $sensorData['getCurrentHighDifference']);
//                    self::assertEquals($readingTypeObject->getMeasurementDifferenceLowReading(), $sensorData['getCurrentLowDifference']);
//
//                    if ($readingTypeObject instanceof Temperature) {
//                        self::assertEquals(Temperature::READING_SYMBOL, $sensorData['readingSymbol']);
//                    }
//                    if ($readingTypeObject instanceof Humidity) {
//                        self::assertEquals(Humidity::READING_SYMBOL, $sensorData['readingSymbol']);
//                    }
//                    if ($readingTypeObject instanceof Latitude) {
//                        self::assertNull($sensorData['readingSymbol']);
//                    }
//                    if ($readingTypeObject instanceof Analog) {
//                        self::assertNull($sensorData['readingSymbol']);
//                    }
//                }
//            }
//
//            self::assertEquals($sensorObject->getSensorName(), $cardData['sensorName'], 'sensor names did not match from the object and the dto response data');
//            self::assertEquals($sensorObject->getSensorTypeID()->getSensorType(), $cardData['sensorType'], 'sensor types did not match from the object and the dto response data');
//            self::assertEquals($sensorObject->getDeviceNameID()->getRoomObject()->getRoom(), $cardData['sensorRoom'], 'sensor room did not match from the object and the dto response data');
//            self::assertEquals($cardViewObject->getCardIconID()->getIconName(), $cardData['cardIcon'], 'sensor card icons did not match from the object and the dto response data');
//            self::assertEquals($cardViewObject->getCardColourID()->getColour(), $cardData['cardColour'], 'sensor card colour did not match from the object and the dto response data');
//            self::assertEquals($cardViewObject->getCardViewID(), $cardData['cardViewID'], 'card view id\'s did not match from the object and the dto response data');
//        }
//
//        self::assertCount($countDeviceCards, $responseData);
//        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
//    }
//
//    public function test_returning_all_card_dto_by_device_with_none_existant_device_id()
//    {
//        while (true) {
//            $invalidDeviceId = random_int(0, 10000);
//            $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceNameID' => $invalidDeviceId]);
//
//            if ($device === null) {
//                break;
//            }
//        }
//
//        $this->client->request(
//            'GET',
//            self::API_CARD_DATA_RETURN_CARD_DTO_ROUTE,
//            ['view' => 'device', 'device-id' => $invalidDeviceId],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//
//        self::assertNull($device);
//        self::assertStringContainsString('No device found', $responseData['payload']['errors'][0]);
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//    // showCardViewForm Controller Method
//    public function test_card_form_user_options()
//    {
//        $cardView = $this->entityManager->getRepository(CardView::class)->findBy(['userID' => $this->testUser])[0];
//        $numberOfIcons = $this->entityManager->getRepository(Icons::class)->countAllIcons();
//        $numberOfColours = $this->entityManager->getRepository(CardColour::class)->countAllColours();
//        $numberOfStates = $this->entityManager->getRepository(Cardstate::class)->countAllStates();
//
//        $this->client->request(
//            'GET',
//            self::API_CARD_VIEW_FORM_DTO_URL,
//            ['cardViewID' => $cardView->getCardViewID()],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
////dd($responseData);
//        self::assertJson($this->client->getResponse()->getContent());
//        self::assertCount($numberOfIcons, $responseData['iconSelection']);
//        self::assertCount($numberOfColours, $responseData['userColourSelections']);
//        self::assertCount($numberOfStates, $responseData['userCardViewSelections']);
//
//        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
//    }
//
//    public function test_cardview_form_dallas_sensor()
//    {
//        $sensorType = SensorType::DALLAS_TEMPERATURE;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $this->client->request(
//            'GET',
//            self::API_CARD_VIEW_FORM_DTO_URL,
//            ['cardViewID' => $cardView->getCardViewID()],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
////dd($this->client->getResponse()->getContent());
//        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
//
//        foreach ($responseData['sensorData'] as $sensorData) {
//            $readingTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\ReadingTypes\\' . ucfirst($sensorData['sensorType']))->findOneBy(['sensorNameID' => $sensorObject]);
//
//            if ($readingTypeObject instanceof StandardReadingSensorInterface) {
//                self::assertEquals($readingTypeObject->getHighReading(), $sensorData['highReading']);
//                self::assertEquals($readingTypeObject->getLowReading(), $sensorData['lowReading']);
//                self::assertEquals($readingTypeObject->getConstRecord(), $sensorData['constRecord']);
//
//                if ($readingTypeObject instanceof Temperature) {
//                    self::assertEquals(Temperature::READING_SYMBOL, $sensorData['readingSymbol']);
//                }
//                if ($readingTypeObject instanceof Humidity) {
//                    self::assertEquals(Humidity::READING_SYMBOL, $sensorData['readingSymbol']);
//                }
//                if ($readingTypeObject instanceof Latitude) {
//                    self::assertNull($sensorData['readingSymbol']);
//                }
//                if ($readingTypeObject instanceof Analog) {
//                    self::assertNull($sensorData['readingSymbol']);
//                }
//            }
//        }
//
//        self::assertEquals($cardView->getCardViewID(), $responseData['cardViewID']);
//        self::assertEquals($cardView->getCardIconID()->getIconID(), $responseData['cardIcon']['iconID']);
//        self::assertEquals($cardView->getCardColourID()->getColourID(), $responseData['cardColour']['colourID']);
//        self::assertEquals($cardView->getCardStateID()->getCardstateID(), $responseData['currentViewState']['cardStateID']);
//
//        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
//    }
//
//
//    public function test_cardview_form_bmp_sensor()
//    {
//        $sensorType = SensorType::BMP_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $this->client->request(
//            'GET',
//            self::API_CARD_VIEW_FORM_DTO_URL,
//            ['cardViewID' => $cardView->getCardViewID()],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
//
//        foreach ($responseData['sensorData'] as $sensorData) {
//            $readingTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\ReadingTypes\\' . ucfirst($sensorData['sensorType']))->findOneBy(['sensorNameID' => $sensorObject]);
//
//            if ($readingTypeObject instanceof StandardReadingSensorInterface) {
//                self::assertEquals($readingTypeObject->getHighReading(), $sensorData['highReading']);
//                self::assertEquals($readingTypeObject->getLowReading(), $sensorData['lowReading']);
//                self::assertEquals($readingTypeObject->getConstRecord(), $sensorData['constRecord']);
//
//                if ($readingTypeObject instanceof Temperature) {
//                    self::assertEquals(Temperature::READING_SYMBOL, $sensorData['readingSymbol']);
//                }
//                if ($readingTypeObject instanceof Humidity) {
//                    self::assertEquals(Humidity::READING_SYMBOL, $sensorData['readingSymbol']);
//                }
//                if ($readingTypeObject instanceof Latitude) {
//                    self::assertNull($sensorData['readingSymbol']);
//                }
//                if ($readingTypeObject instanceof Analog) {
//                    self::assertNull($sensorData['readingSymbol']);
//                }
//            }
//        }
//
//        self::assertEquals($cardView->getCardViewID(), $responseData['cardViewID']);
//        self::assertEquals($cardView->getCardIconID()->getIconID(), $responseData['cardIcon']['iconID']);
//        self::assertEquals($cardView->getCardColourID()->getColourID(), $responseData['cardColour']['colourID']);
//        self::assertEquals($cardView->getCardStateID()->getCardstateID(), $responseData['currentViewState']['cardStateID']);
//
//        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
//    }
//
//    public function test_cardview_form_dht_sensor()
//    {
//        $sensorType = SensorType::DHT_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $this->client->request(
//            'GET',
//            self::API_CARD_VIEW_FORM_DTO_URL,
//            ['cardViewID' => $cardView->getCardViewID()],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
//
//        foreach ($responseData['sensorData'] as $sensorData) {
//            $readingTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\ReadingTypes\\' . ucfirst($sensorData['sensorType']))->findOneBy(['sensorNameID' => $sensorObject]);
//
//            if ($readingTypeObject instanceof StandardReadingSensorInterface) {
//                self::assertEquals($readingTypeObject->getHighReading(), $sensorData['highReading']);
//                self::assertEquals($readingTypeObject->getLowReading(), $sensorData['lowReading']);
//                self::assertEquals($readingTypeObject->getConstRecord(), $sensorData['constRecord']);
//
//                if ($readingTypeObject instanceof Temperature) {
//                    self::assertEquals(Temperature::READING_SYMBOL, $sensorData['readingSymbol']);
//                }
//                if ($readingTypeObject instanceof Humidity) {
//                    self::assertEquals(Humidity::READING_SYMBOL, $sensorData['readingSymbol']);
//                }
//                if ($readingTypeObject instanceof Latitude) {
//                    self::assertNull($sensorData['readingSymbol']);
//                }
//                if ($readingTypeObject instanceof Analog) {
//                    self::assertNull($sensorData['readingSymbol']);
//                }
//            }
//        }
//
//        self::assertEquals($cardView->getCardViewID(), $responseData['cardViewID']);
//        self::assertEquals($cardView->getCardIconID()->getIconID(), $responseData['cardIcon']['iconID']);
//        self::assertEquals($cardView->getCardColourID()->getColourID(), $responseData['cardColour']['colourID']);
//        self::assertEquals($cardView->getCardStateID()->getCardstateID(), $responseData['currentViewState']['cardStateID']);
//
//        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
//    }
//
//    public function test_cardview_form_soil_sensor()
//    {
//        $sensorType = SensorType::SOIL_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $this->client->request(
//            'GET',
//            self::API_CARD_VIEW_FORM_DTO_URL,
//            ['cardViewID' => $cardView->getCardViewID()],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
//
//        foreach ($responseData['sensorData'] as $sensorData) {
//            $readingTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\ReadingTypes\\' . ucfirst($sensorData['sensorType']))->findOneBy(['sensorNameID' => $sensorObject]);
//
//            if ($readingTypeObject instanceof StandardReadingSensorInterface) {
//                self::assertEquals($readingTypeObject->getHighReading(), $sensorData['highReading']);
//                self::assertEquals($readingTypeObject->getLowReading(), $sensorData['lowReading']);
//                self::assertEquals($readingTypeObject->getConstRecord(), $sensorData['constRecord']);
//
//                if ($readingTypeObject instanceof Temperature) {
//                    self::assertEquals(Temperature::READING_SYMBOL, $sensorData['readingSymbol']);
//                }
//                if ($readingTypeObject instanceof Humidity) {
//                    self::assertEquals(Humidity::READING_SYMBOL, $sensorData['readingSymbol']);
//                }
//                if ($readingTypeObject instanceof Latitude) {
//                    self::assertNull($sensorData['readingSymbol']);
//                }
//                if ($readingTypeObject instanceof Analog) {
//                    self::assertNull($sensorData['readingSymbol']);
//                }
//            }
//        }
//
//        self::assertEquals($cardView->getCardViewID(), $responseData['cardViewID']);
//        self::assertEquals($cardView->getCardIconID()->getIconID(), $responseData['cardIcon']['iconID']);
//        self::assertEquals($cardView->getCardColourID()->getColourID(), $responseData['cardColour']['colourID']);
//        self::assertEquals($cardView->getCardStateID()->getCardstateID(), $responseData['currentViewState']['cardStateID']);
//
//        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
//    }
//
//
//    public function test_returning_all_card_dto_index_no_cards_in_off_state()
//    {
//        $offCardState = $this->entityManager->getRepository(Cardstate::class)->findOneBy(['state' => Cardstate::OFF]);
//        $cardsInOffState = $this->entityManager->getRepository(CardView::class)->findBy(['cardStateID' => $offCardState, 'userID' => $this->testUser]);
//
//
//        $this->client->request(
//            'GET',
//            self::API_CARD_DATA_RETURN_CARD_DTO_ROUTE,
//            ['view' => 'index'],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
//
//        foreach ($cardsInOffState as $indexCard) {
//            foreach ($responseData as $responseCardViewID) {
//                self::assertNotEquals($responseCardViewID['cardViewID'], $indexCard->getCardViewID(), 'A card with the state set to false has been found in the index dto sensor name: ' . $indexCard->getSensorNameID()->getSensorName());
//            }
//        }
//    }
//
//
//    public function test_cannot_view_not_owened_card_view()
//    {
//        $cards = $this->entityManager->getRepository(CardView::class)->findAll();
//
//        foreach ($cards as $card) {
//            if ($card->getUserID()->getUserID() !== $this->testUser->getUserID()) {
//                $cardNotOwnedByUser = $card;
//                break;
//            }
//        }
//
//        $formData = [
//            'cardViewID' => $cardNotOwnedByUser->getCardViewID(),
//        ];
//
//        $this->client->request(
//            'GET',
//            self::API_CARD_VIEW_FORM_DTO_URL,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//
//        self::assertStringContainsString(APIErrorMessages::ACCESS_DENIED, $responseData['payload']['errors'][0]);
//        self::assertStringContainsString('You Are Not Authorised To Be Here', $responseData['title']);
//
//        self::assertEquals(HTTPStatusCodes::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
//    }

    //updateCardView Tests
//    public function test_can_update_card_view_form_all_selections_bmp()
//    {
//        $sensorType = SensorType::BMP_SENSOR;
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//        ];
//
//        $sensorReadingTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . ucfirst($sensorType))->findOneBy(['sensorNameID' => $sensorObject]);
//
//        $sensorName = $sensorReadingTypeObject->getSensorObject()->getSensorName();
//        if ($sensorReadingTypeObject instanceof TemperatureSensorTypeInterface) {
//            $temperatureObject = $sensorReadingTypeObject->getTempObject();
//
//            $formData = array_merge($formData, [
//                'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
//                'temperature-low-reading' => $temperatureObject->getLowReading() + 1,
//                'temperature-const-record' => true,
//            ]);
//        }
//        if($sensorReadingTypeObject instanceof HumiditySensorTypeInterface) {
//            $humidityObject = $sensorReadingTypeObject->getHumidObject();
//
//            $formData = array_merge($formData, [
//                'humidity-high-reading' => $humidityObject->getHighReading() + 1,
//                'humidity-low-reading' => $humidityObject->getLowReading() + 1,
//                'humidity-const-record' => true,
//            ]);
//        }
//        if($sensorReadingTypeObject instanceof LatitudeSensorTypeInterface) {
//            $latitudeObject = $sensorReadingTypeObject->getLatitudeObject();
//
//            $formData = array_merge($formData, [
//                'latitude-high-reading' => $latitudeObject->getHighReading() + 1,
//                'latitude-low-reading' => $latitudeObject->getLowReading() + 1,
//                'latitude-const-record' => true,
//            ]);
//        }
//        if($sensorReadingTypeObject instanceof AnalogSensorTypeInterface) {
//            $analogObject = $sensorReadingTypeObject->getAnalogObject();
//
//            $formData = array_merge($formData, [
//                'analog-high-reading' => $analogObject->getHighReading() - 1,
//                'analog-low-reading' => $analogObject->getLowReading() - 1,
//                'analog-const-record' => true,
//            ]);
//        }
//
//        $this->client->request(
//            'PUT',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $sensorReadingTypeAfter = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . ucfirst($sensorType))->findOneBy(['sensorNameID' => $sensorObject]);
//
//        $cardViewObjectAfter = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $readingFailureMessage = "%s %s reading failed for sensor: %s";
//        $constFailureMessage = sprintf("const record failed for sensor: %s", '$sensorName');
//
//        if ($sensorReadingTypeObject instanceof TemperatureSensorTypeInterface) {
//            $temperatureObjectAfter = $sensorReadingTypeAfter->getTempObject();
//            self::assertEquals($formData['temperature-high-reading'], $temperatureObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'temperature', 'high', $sensorName));
//            self::assertEquals($formData['temperature-low-reading'], $temperatureObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'temperature', 'low', $sensorName));
//            self::assertEquals($formData['temperature-const-record'], $temperatureObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//        if($sensorReadingTypeObject instanceof HumiditySensorTypeInterface) {
//            $humidityObjectAfter = $sensorReadingTypeAfter->getHumidObject();
//
//            self::assertEquals($formData['humidity-high-reading'], $humidityObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'humidity', 'high', $sensorName));
//            self::assertEquals($formData['humidity-low-reading'], $humidityObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'humidity', 'low', $sensorName));
//            self::assertEquals($formData['humidity-const-record'], $humidityObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//        if($sensorReadingTypeObject instanceof LatitudeSensorTypeInterface) {
//            $latitudeObjectAfter = $sensorReadingTypeAfter->getLatitudeObject();
//            self::assertEquals($formData['latitude-high-reading'], $latitudeObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'latitude', 'high', $sensorName));
//            self::assertEquals($formData['latitude-low-reading'], $latitudeObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'latitude', 'low', $sensorName));
//            self::assertEquals($formData['latitude-const-record'], $latitudeObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//        if($sensorReadingTypeObject instanceof AnalogSensorTypeInterface) {
//            $analogObjectAfter = $sensorReadingTypeAfter->getAnalogObject();
//            self::assertEquals($formData['analog-high-reading'], $analogObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'analog', 'high', $sensorName));
//            self::assertEquals($formData['analog-low-reading'], $analogObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'analog', 'low', $sensorName));
//            self::assertEquals($formData['analog-const-record'], $analogObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//
//        $cardErrorMessage = "%s id did not match for: %s";
//
//        self::assertEquals($formData['cardColour'], $cardViewObjectAfter->getCardColourID()->getColourID(), sprintf($cardErrorMessage, 'colour', $sensorName));
//        self::assertEquals($formData['cardIcon'], $cardViewObjectAfter->getCardIconID()->getIconID(), sprintf($cardErrorMessage, 'icon', $sensorName));
//        self::assertEquals($formData['cardViewState'], $cardViewObjectAfter->getCardStateID()->getCardstateID(), sprintf($cardErrorMessage, 'card state', $sensorName));
//
//        self::assertEquals(HTTPStatusCodes::HTTP_UPDATED_SUCCESSFULLY, $this->client->getResponse()->getStatusCode());
//    }
//
//    public function test_can_update_card_view_form_all_selections_dallas()
//    {
//        $sensorType = SensorType::DALLAS_TEMPERATURE;
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//        ];
//
//        $sensorReadingTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . ucfirst($sensorType))->findOneBy(['sensorNameID' => $sensorObject]);
//
//        $sensorName = $sensorReadingTypeObject->getSensorObject()->getSensorName();
//        if ($sensorReadingTypeObject instanceof TemperatureSensorTypeInterface) {
//            $temperatureObject = $sensorReadingTypeObject->getTempObject();
//
//            $formData = array_merge($formData, [
//                'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
//                'temperature-low-reading' => $temperatureObject->getLowReading() + 1,
//                'temperature-const-record' => true,
//            ]);
//        }
//        if($sensorReadingTypeObject instanceof HumiditySensorTypeInterface) {
//            $humidityObject = $sensorReadingTypeObject->getHumidObject();
//
//            $formData = array_merge($formData, [
//                'humidity-high-reading' => $humidityObject->getHighReading() + 1,
//                'humidity-low-reading' => $humidityObject->getLowReading() + 1,
//                'humidity-const-record' => true,
//            ]);
//        }
//        if($sensorReadingTypeObject instanceof LatitudeSensorTypeInterface) {
//            $latitudeObject = $sensorReadingTypeObject->getLatitudeObject();
//
//            $formData = array_merge($formData, [
//                'latitude-high-reading' => $latitudeObject->getHighReading() + 1,
//                'latitude-low-reading' => $latitudeObject->getLowReading() + 1,
//                'latitude-const-record' => true,
//            ]);
//        }
//        if($sensorReadingTypeObject instanceof AnalogSensorTypeInterface) {
//            $analogObject = $sensorReadingTypeObject->getAnalogObject();
//
//            $formData = array_merge($formData, [
//                'analog-high-reading' => $analogObject->getHighReading() - 1,
//                'analog-low-reading' => $analogObject->getLowReading() - 1,
//                'analog-const-record' => true,
//            ]);
//        }
//
//        $this->client->request(
//            'PUT',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $sensorReadingTypeAfter = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . ucfirst($sensorType))->findOneBy(['sensorNameID' => $sensorObject]);
//
//        $cardViewObjectAfter = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $readingFailureMessage = "%s %s reading failed for sensor: %s";
//        $constFailureMessage = sprintf("const record failed for sensor: %s", '$sensorName');
//
//        if ($sensorReadingTypeObject instanceof TemperatureSensorTypeInterface) {
//            $temperatureObjectAfter = $sensorReadingTypeAfter->getTempObject();
//            self::assertEquals($formData['temperature-high-reading'], $temperatureObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'temperature', 'high', $sensorName));
//            self::assertEquals($formData['temperature-low-reading'], $temperatureObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'temperature', 'low', $sensorName));
//            self::assertEquals($formData['temperature-const-record'], $temperatureObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//        if($sensorReadingTypeObject instanceof HumiditySensorTypeInterface) {
//            $humidityObjectAfter = $sensorReadingTypeAfter->getHumidObject();
//
//            self::assertEquals($formData['humidity-high-reading'], $humidityObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'humidity', 'high', $sensorName));
//            self::assertEquals($formData['humidity-low-reading'], $humidityObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'humidity', 'low', $sensorName));
//            self::assertEquals($formData['humidity-const-record'], $humidityObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//        if($sensorReadingTypeObject instanceof LatitudeSensorTypeInterface) {
//            $latitudeObjectAfter = $sensorReadingTypeAfter->getLatitudeObject();
//            self::assertEquals($formData['latitude-high-reading'], $latitudeObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'latitude', 'high', $sensorName));
//            self::assertEquals($formData['latitude-low-reading'], $latitudeObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'latitude', 'low', $sensorName));
//            self::assertEquals($formData['latitude-const-record'], $latitudeObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//        if($sensorReadingTypeObject instanceof AnalogSensorTypeInterface) {
//            $analogObjectAfter = $sensorReadingTypeAfter->getAnalogObject();
//            self::assertEquals($formData['analog-high-reading'], $analogObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'analog', 'high', $sensorName));
//            self::assertEquals($formData['analog-low-reading'], $analogObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'analog', 'low', $sensorName));
//            self::assertEquals($formData['analog-const-record'], $analogObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//
//        $cardErrorMessage = "%s id did not match for: %s";
//
//        self::assertEquals($formData['cardColour'], $cardViewObjectAfter->getCardColourID()->getColourID(), sprintf($cardErrorMessage, 'colour', $sensorName));
//        self::assertEquals($formData['cardIcon'], $cardViewObjectAfter->getCardIconID()->getIconID(), sprintf($cardErrorMessage, 'icon', $sensorName));
//        self::assertEquals($formData['cardViewState'], $cardViewObjectAfter->getCardStateID()->getCardstateID(), sprintf($cardErrorMessage, 'card state', $sensorName));
//
//        self::assertEquals(HTTPStatusCodes::HTTP_UPDATED_SUCCESSFULLY, $this->client->getResponse()->getStatusCode());
//    }
//
//
//    public function test_can_update_card_view_form_all_selections_soil()
//    {
//        $sensorType = SensorType::SOIL_SENSOR;
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//        ];
//
//        $sensorReadingTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . ucfirst($sensorType))->findOneBy(['sensorNameID' => $sensorObject]);
//
//        $sensorName = $sensorReadingTypeObject->getSensorObject()->getSensorName();
//        if ($sensorReadingTypeObject instanceof TemperatureSensorTypeInterface) {
//            $temperatureObject = $sensorReadingTypeObject->getTempObject();
//
//            $formData = array_merge($formData, [
//                'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
//                'temperature-low-reading' => $temperatureObject->getLowReading() + 1,
//                'temperature-const-record' => true,
//            ]);
//        }
//        if($sensorReadingTypeObject instanceof HumiditySensorTypeInterface) {
//            $humidityObject = $sensorReadingTypeObject->getHumidObject();
//
//            $formData = array_merge($formData, [
//                'humidity-high-reading' => $humidityObject->getHighReading() + 1,
//                'humidity-low-reading' => $humidityObject->getLowReading() + 1,
//                'humidity-const-record' => true,
//            ]);
//        }
//        if($sensorReadingTypeObject instanceof LatitudeSensorTypeInterface) {
//            $latitudeObject = $sensorReadingTypeObject->getLatitudeObject();
//
//            $formData = array_merge($formData, [
//                'latitude-high-reading' => $latitudeObject->getHighReading() + 1,
//                'latitude-low-reading' => $latitudeObject->getLowReading() + 1,
//                'latitude-const-record' => true,
//            ]);
//        }
//        if($sensorReadingTypeObject instanceof AnalogSensorTypeInterface) {
//            $analogObject = $sensorReadingTypeObject->getAnalogObject();
//
//            $formData = array_merge($formData, [
//                'analog-high-reading' => $analogObject->getHighReading() - 1,
//                'analog-low-reading' => $analogObject->getLowReading() - 1,
//                'analog-const-record' => true,
//            ]);
//        }
//
//        $this->client->request(
//            'PUT',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $sensorReadingTypeAfter = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . ucfirst($sensorType))->findOneBy(['sensorNameID' => $sensorObject]);
//
//        $cardViewObjectAfter = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $readingFailureMessage = "%s %s reading failed for sensor: %s";
//        $constFailureMessage = sprintf("const record failed for sensor: %s", '$sensorName');
//
//        if ($sensorReadingTypeObject instanceof TemperatureSensorTypeInterface) {
//            $temperatureObjectAfter = $sensorReadingTypeAfter->getTempObject();
//            self::assertEquals($formData['temperature-high-reading'], $temperatureObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'temperature', 'high', $sensorName));
//            self::assertEquals($formData['temperature-low-reading'], $temperatureObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'temperature', 'low', $sensorName));
//            self::assertEquals($formData['temperature-const-record'], $temperatureObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//        if($sensorReadingTypeObject instanceof HumiditySensorTypeInterface) {
//            $humidityObjectAfter = $sensorReadingTypeAfter->getHumidObject();
//
//            self::assertEquals($formData['humidity-high-reading'], $humidityObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'humidity', 'high', $sensorName));
//            self::assertEquals($formData['humidity-low-reading'], $humidityObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'humidity', 'low', $sensorName));
//            self::assertEquals($formData['humidity-const-record'], $humidityObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//        if($sensorReadingTypeObject instanceof LatitudeSensorTypeInterface) {
//            $latitudeObjectAfter = $sensorReadingTypeAfter->getLatitudeObject();
//            self::assertEquals($formData['latitude-high-reading'], $latitudeObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'latitude', 'high', $sensorName));
//            self::assertEquals($formData['latitude-low-reading'], $latitudeObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'latitude', 'low', $sensorName));
//            self::assertEquals($formData['latitude-const-record'], $latitudeObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//        if($sensorReadingTypeObject instanceof AnalogSensorTypeInterface) {
//            $analogObjectAfter = $sensorReadingTypeAfter->getAnalogObject();
//            self::assertEquals($formData['analog-high-reading'], $analogObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'analog', 'high', $sensorName));
//            self::assertEquals($formData['analog-low-reading'], $analogObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'analog', 'low', $sensorName));
//            self::assertEquals($formData['analog-const-record'], $analogObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//
//        $cardErrorMessage = "%s id did not match for: %s";
//
//        self::assertEquals($formData['cardColour'], $cardViewObjectAfter->getCardColourID()->getColourID(), sprintf($cardErrorMessage, 'colour', $sensorName));
//        self::assertEquals($formData['cardIcon'], $cardViewObjectAfter->getCardIconID()->getIconID(), sprintf($cardErrorMessage, 'icon', $sensorName));
//        self::assertEquals($formData['cardViewState'], $cardViewObjectAfter->getCardStateID()->getCardstateID(), sprintf($cardErrorMessage, 'card state', $sensorName));
//
//        self::assertEquals(HTTPStatusCodes::HTTP_UPDATED_SUCCESSFULLY, $this->client->getResponse()->getStatusCode());
//    }
//
//    public function test_can_update_card_view_form_all_selections_dht()
//    {
//        $sensorType = SensorType::DHT_SENSOR;
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//        ];
//
//        $sensorReadingTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . ucfirst($sensorType))->findOneBy(['sensorNameID' => $sensorObject]);
//
//        $sensorName = $sensorReadingTypeObject->getSensorObject()->getSensorName();
//        if ($sensorReadingTypeObject instanceof TemperatureSensorTypeInterface) {
//            $temperatureObject = $sensorReadingTypeObject->getTempObject();
//
//            $formData = array_merge($formData, [
//                'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
//                'temperature-low-reading' => $temperatureObject->getLowReading() + 1,
//                'temperature-const-record' => true,
//            ]);
//        }
//        if($sensorReadingTypeObject instanceof HumiditySensorTypeInterface) {
//            $humidityObject = $sensorReadingTypeObject->getHumidObject();
//
//            $formData = array_merge($formData, [
//                'humidity-high-reading' => $humidityObject->getHighReading() + 1,
//                'humidity-low-reading' => $humidityObject->getLowReading() + 1,
//                'humidity-const-record' => true,
//            ]);
//        }
//        if($sensorReadingTypeObject instanceof LatitudeSensorTypeInterface) {
//            $latitudeObject = $sensorReadingTypeObject->getLatitudeObject();
//
//            $formData = array_merge($formData, [
//                'latitude-high-reading' => $latitudeObject->getHighReading() + 1,
//                'latitude-low-reading' => $latitudeObject->getLowReading() + 1,
//                'latitude-const-record' => true,
//            ]);
//        }
//        if($sensorReadingTypeObject instanceof AnalogSensorTypeInterface) {
//            $analogObject = $sensorReadingTypeObject->getAnalogObject();
//
//            $formData = array_merge($formData, [
//                'analog-high-reading' => $analogObject->getHighReading() - 1,
//                'analog-low-reading' => $analogObject->getLowReading() - 1,
//                'analog-const-record' => true,
//            ]);
//        }
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $sensorReadingTypeAfter = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . ucfirst($sensorType))->findOneBy(['sensorNameID' => $sensorObject]);
//
//        $cardViewObjectAfter = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $readingFailureMessage = "%s %s reading failed for sensor: %s";
//        $constFailureMessage = sprintf("const record failed for sensor: %s", '$sensorName');
//
//        if ($sensorReadingTypeObject instanceof TemperatureSensorTypeInterface) {
//            $temperatureObjectAfter = $sensorReadingTypeAfter->getTempObject();
//            self::assertEquals($formData['temperature-high-reading'], $temperatureObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'temperature', 'high', $sensorName));
//            self::assertEquals($formData['temperature-low-reading'], $temperatureObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'temperature', 'low', $sensorName));
//            self::assertEquals($formData['temperature-const-record'], $temperatureObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//        if($sensorReadingTypeObject instanceof HumiditySensorTypeInterface) {
//            $humidityObjectAfter = $sensorReadingTypeAfter->getHumidObject();
//
//            self::assertEquals($formData['humidity-high-reading'], $humidityObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'humidity', 'high', $sensorName));
//            self::assertEquals($formData['humidity-low-reading'], $humidityObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'humidity', 'low', $sensorName));
//            self::assertEquals($formData['humidity-const-record'], $humidityObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//        if($sensorReadingTypeObject instanceof LatitudeSensorTypeInterface) {
//            $latitudeObjectAfter = $sensorReadingTypeAfter->getLatitudeObject();
//            self::assertEquals($formData['latitude-high-reading'], $latitudeObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'latitude', 'high', $sensorName));
//            self::assertEquals($formData['latitude-low-reading'], $latitudeObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'latitude', 'low', $sensorName));
//            self::assertEquals($formData['latitude-const-record'], $latitudeObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//        if($sensorReadingTypeObject instanceof AnalogSensorTypeInterface) {
//            $analogObjectAfter = $sensorReadingTypeAfter->getAnalogObject();
//            self::assertEquals($formData['analog-high-reading'], $analogObjectAfter->getHighReading(), sprintf($readingFailureMessage, 'analog', 'high', $sensorName));
//            self::assertEquals($formData['analog-low-reading'], $analogObjectAfter->getLowReading(), sprintf($readingFailureMessage, 'analog', 'low', $sensorName));
//            self::assertEquals($formData['analog-const-record'], $analogObjectAfter->getConstRecord(), $constFailureMessage);
//        }
//
//        $cardErrorMessage = "%s id did not match for: %s";
//
//        self::assertEquals($formData['cardColour'], $cardViewObjectAfter->getCardColourID()->getColourID(), sprintf($cardErrorMessage, 'colour', $sensorName));
//        self::assertEquals($formData['cardIcon'], $cardViewObjectAfter->getCardIconID()->getIconID(), sprintf($cardErrorMessage, 'icon', $sensorName));
//        self::assertEquals($formData['cardViewState'], $cardViewObjectAfter->getCardStateID()->getCardstateID(), sprintf($cardErrorMessage, 'card state', $sensorName));
//
//        self::assertEquals(HTTPStatusCodes::HTTP_UPDATED_SUCCESSFULLY, $this->client->getResponse()->getStatusCode());
//    }
//
//
//
//    //updateCardView Tests Wrong Data
//
//    // Temperature
//    public function test_can_not_update_card_view_form_temperature_selections_outofrange_data_bmp()
//    {
//        $sensorType = SensorType::BMP_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $sensorTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//        $humidityObject = $sensorTypeObject->getHumidObject();
//        $latitudeObject = $sensorTypeObject->getLatitudeObject();
//
//        $highReading = 90;
//        $lowReading = -50;
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//
//            'temperature-high-reading' => $highReading,
//            'temperature-low-reading' => $lowReading,
//            'temperature-const-record' => true,
//
//            'humidity-high-reading' => $humidityObject->getLowReading() + 1,
//            'humidity-low-reading' => $humidityObject->getHighReading() + 1,
//            'humidity-const-record' => true,
//
//            'latitude-high-reading' => $latitudeObject->getHighReading() + 1,
//            'latitude-low-reading' => $latitudeObject->getLowReading() + 1,
//            'latitude-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $sensorTypeObjectAfter = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//        $highString = "%s settings for %s sensor cannot exceed %u%s you entered %s%s";
//        $lowString = "%s settings for %s sensor cannot be below %s%s you entered %s%s";
//
//        self::assertStringContainsString(sprintf(
//            $highString,
//            'Temperature',
//            $sensorType,
//            Bmp::HIGH_TEMPERATURE_READING_BOUNDRY,
//            Temperature::READING_SYMBOL,
//            $highReading,
//            Temperature::READING_SYMBOL
//        ), $responseData['payload']['errors'][0]);
//
//
//        self::assertStringContainsString(sprintf(
//            $lowString,
//            'Temperature',
//            $sensorType,
//            Bmp::LOW_TEMPERATURE_READING_BOUNDRY,
//            Temperature::READING_SYMBOL,
//            $lowReading,
//            Temperature::READING_SYMBOL
//        ), $responseData['payload']['errors'][1]);
//
//        $temperatureObject = $sensorTypeObjectAfter->getTempObject();
//        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
//        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
//        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());
//
//        $humidityObject = $sensorTypeObjectAfter->getHumidObject();
//        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
//        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
//        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());
//
//        $latitudeObject = $sensorTypeObjectAfter->getLatitudeObject();
//        self::assertNotEquals($formData['latitude-high-reading'], $latitudeObject->getHighReading());
//        self::assertNotEquals($formData['latitude-low-reading'], $latitudeObject->getLowReading());
//        self::assertNotEquals($formData['latitude-const-record'], $latitudeObject->getConstRecord());
//
//        self::assertNotEquals($formData['cardColour'], $cardViewObject->getCardColourID()->getColourID());
//        self::assertNotEquals($formData['cardIcon'], $cardViewObject->getCardIconID()->getIconID());
//        self::assertNotEquals($formData['cardViewState'], $cardViewObject->getCardStateID()->getCardstateID());
//
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//
//    public function test_can_not_update_card_view_form_temperature_selections_outofrange_data_dallas()
//    {
//        $sensorType = SensorType::DALLAS_TEMPERATURE;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $highReading = 130;
//        $lowReading = -60;
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//
//            'temperature-high-reading' => $highReading,
//            'temperature-low-reading' => $lowReading,
//            'temperature-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $sensorTypeObjectAfter = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//        $highString = "%s settings for %s sensor cannot exceed %u%s you entered %s%s";
//        $lowString = "%s settings for %s sensor cannot be below %s%s you entered %s%s";
//
//        self::assertStringContainsString(sprintf(
//            $highString,
//            'Temperature',
//            $sensorType,
//            Dallas::HIGH_TEMPERATURE_READING_BOUNDRY,
//            Temperature::READING_SYMBOL,
//            $highReading,
//            Temperature::READING_SYMBOL
//        ), $responseData['payload']['errors'][0]);
//
//
//        self::assertStringContainsString(sprintf(
//            $lowString,
//            'Temperature',
//            $sensorType,
//            Dallas::LOW_TEMPERATURE_READING_BOUNDRY,
//            Temperature::READING_SYMBOL,
//            $lowReading,
//            Temperature::READING_SYMBOL
//        ), $responseData['payload']['errors'][1]);
//
//        $temperatureObject = $sensorTypeObjectAfter->getTempObject();
//        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
//        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
//        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());
//
//        self::assertNotEquals($formData['cardColour'], $cardViewObject->getCardColourID()->getColourID());
//        self::assertNotEquals($formData['cardIcon'], $cardViewObject->getCardIconID()->getIconID());
//        self::assertNotEquals($formData['cardViewState'], $cardViewObject->getCardStateID()->getCardstateID());
//
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//
//    public function test_can_not_update_card_view_form_temperature_selections_outofrange_data_dht()
//    {
//        $sensorType = SensorType::DHT_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $sensorTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//        $humidityObject = $sensorTypeObject->getHumidObject();
//
//        $highReading = 85;
//        $lowReading = -45;
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//
//            'temperature-high-reading' => $highReading,
//            'temperature-low-reading' => $lowReading,
//            'temperature-const-record' => true,
//
//            'humidity-high-reading' => $humidityObject->getLowReading() + 1,
//            'humidity-low-reading' => $humidityObject->getHighReading() + 1,
//            'humidity-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $sensorTypeObjectAfter = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//
//        $highString = "%s settings for %s sensor cannot exceed %u%s you entered %s%s";
//        $lowString = "%s settings for %s sensor cannot be below %s%s you entered %s%s";
//
//        self::assertStringContainsString(sprintf(
//            $highString,
//            'Temperature',
//            $sensorType,
//            Dht::HIGH_TEMPERATURE_READING_BOUNDRY,
//            Temperature::READING_SYMBOL,
//            $highReading,
//            Temperature::READING_SYMBOL
//        ), $responseData['payload']['errors'][0]);
//
//
//        self::assertStringContainsString(sprintf(
//            $lowString,
//            'Temperature',
//            $sensorType,
//            Dht::LOW_TEMPERATURE_READING_BOUNDRY,
//            Temperature::READING_SYMBOL,
//            $lowReading,
//            Temperature::READING_SYMBOL
//        ), $responseData['payload']['errors'][1]);
//
//        $temperatureObject = $sensorTypeObjectAfter->getTempObject();
//        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
//        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
//        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());
//
//        $humidityObject = $sensorTypeObjectAfter->getHumidObject();
//        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
//        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
//        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());
//
//        self::assertNotEquals($formData['cardColour'], $cardViewObject->getCardColourID()->getColourID());
//        self::assertNotEquals($formData['cardIcon'], $cardViewObject->getCardIconID()->getIconID());
//        self::assertNotEquals($formData['cardViewState'], $cardViewObject->getCardStateID()->getCardstateID());
//
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//
//    public function test_can_not_update_card_view_form_temperature_selections_outofrange_high_low_dht()
//    {
//        $sensorType = SensorType::DHT_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
////        $sensorTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//
//        $highReading = 20;
//        $lowReading = 30;
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//
//            'temperature-high-reading' => $highReading,
//            'temperature-low-reading' => $lowReading,
//            'temperature-const-record' => true,
//
//            'humidity-high-reading' => $highReading,
//            'humidity-low-reading' => $lowReading,
//            'humidity-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $sensorTypeObjectAfter = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//
//        self::assertStringContainsString('High reading for temperature cannot be lower than low reading', $responseData['payload']['errors'][0]);
//        self::assertStringContainsString('High reading for humidity cannot be lower than low reading', $responseData['payload']['errors'][1]);
//
//        $temperatureObject = $sensorTypeObjectAfter->getTempObject();
//        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
//        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
//        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());
//
//        $humidityObject = $sensorTypeObjectAfter->getHumidObject();
//        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
//        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
//        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());
//
//        self::assertNotEquals($formData['cardColour'], $cardViewObject->getCardColourID()->getColourID());
//        self::assertNotEquals($formData['cardIcon'], $cardViewObject->getCardIconID()->getIconID());
//        self::assertNotEquals($formData['cardViewState'], $cardViewObject->getCardStateID()->getCardstateID());
//
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//
//    // Humidity
//    public function test_can_not_update_card_view_form_humidity_selections_outofrange_data_bmp()
//    {
//        $sensorType = SensorType::BMP_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $sensorTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//        $temperatureObject = $sensorTypeObject->getTempObject();
//        $latitudeObject = $sensorTypeObject->getLatitudeObject();
//
//        $highReading = 110;
//        $lowReading = -5;
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//
//            'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
//            'temperature-low-reading' => $temperatureObject->getLowReading() +1,
//            'temperature-const-record' => true,
//
//            'humidity-high-reading' => $highReading,
//            'humidity-low-reading' => $lowReading,
//            'humidity-const-record' => true,
//
//            'latitude-high-reading' => $latitudeObject->getHighReading() + 1,
//            'latitude-low-reading' => $latitudeObject->getLowReading() + 1,
//            'latitude-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $sensorTypeObjectAfter = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//        $highString = "Humidity for this sensor cannot be over 100 you entered %s%s";
//        $lowString = "Humidity for this sensor cannot be under 0 you entered %s%s";
//
//        self::assertStringContainsString(sprintf(
//            $highString,
//            $highReading,
//            Humidity::READING_SYMBOL
//        ), $responseData['payload']['errors'][0]);
//
//
//        self::assertStringContainsString(sprintf(
//            $lowString,
//            $lowReading,
//            Humidity::READING_SYMBOL
//        ), $responseData['payload']['errors'][1]);
//
//        $temperatureObject = $sensorTypeObjectAfter->getTempObject();
//        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
//        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
//        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());
//
//        $humidityObject = $sensorTypeObjectAfter->getHumidObject();
//        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
//        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
//        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());
//
//        $latitudeObject = $sensorTypeObjectAfter->getLatitudeObject();
//        self::assertNotEquals($formData['latitude-high-reading'], $latitudeObject->getHighReading());
//        self::assertNotEquals($formData['latitude-low-reading'], $latitudeObject->getLowReading());
//        self::assertNotEquals($formData['latitude-const-record'], $latitudeObject->getConstRecord());
//
//        self::assertNotEquals($formData['cardColour'], $cardViewObject->getCardColourID()->getColourID());
//        self::assertNotEquals($formData['cardIcon'], $cardViewObject->getCardIconID()->getIconID());
//        self::assertNotEquals($formData['cardViewState'], $cardViewObject->getCardStateID()->getCardstateID());
//
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//    public function test_can_not_update_card_view_form_humidity_selections_outofrange_data_dht()
//    {
//        $sensorType = SensorType::DHT_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $sensorTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//        $temperatureObject = $sensorTypeObject->getTempObject();
//
//        $highReading = 110;
//        $lowReading = -5;
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//
//            'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
//            'temperature-low-reading' => $temperatureObject->getLowReading() +1,
//            'temperature-const-record' => true,
//
//            'humidity-high-reading' => $highReading,
//            'humidity-low-reading' => $lowReading,
//            'humidity-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $sensorTypeObjectAfter = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//        $highString = "Humidity for this sensor cannot be over 100 you entered %s%s";
//        $lowString = "Humidity for this sensor cannot be under 0 you entered %s%s";
//
//        self::assertStringContainsString(sprintf(
//            $highString,
//            $highReading,
//            Humidity::READING_SYMBOL
//        ), $responseData['payload']['errors'][0]);
//
//
//        self::assertStringContainsString(sprintf(
//            $lowString,
//            $lowReading,
//            Humidity::READING_SYMBOL
//        ), $responseData['payload']['errors'][1]);
//
//        $temperatureObject = $sensorTypeObjectAfter->getTempObject();
//        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
//        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
//        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());
//
//        $humidityObject = $sensorTypeObjectAfter->getHumidObject();
//        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
//        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
//        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());
//
//        self::assertNotEquals($formData['cardColour'], $cardViewObject->getCardColourID()->getColourID());
//        self::assertNotEquals($formData['cardIcon'], $cardViewObject->getCardIconID()->getIconID());
//        self::assertNotEquals($formData['cardViewState'], $cardViewObject->getCardStateID()->getCardstateID());
//
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//    public function test_can_not_update_card_view_form_humidity_selections_high_low_data_dht()
//    {
//        $sensorType = SensorType::DHT_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $sensorTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//        $temperatureObject = $sensorTypeObject->getTempObject();
//
//        $highReading = 30;
//        $lowReading = 40;
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//
//            'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
//            'temperature-low-reading' => $temperatureObject->getLowReading() +1,
//            'temperature-const-record' => true,
//
//            'humidity-high-reading' => $highReading,
//            'humidity-low-reading' => $lowReading,
//            'humidity-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $sensorTypeObjectAfter = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//
//        self::assertStringContainsString("High reading for humidity cannot be lower than low reading", $responseData['payload']['errors'][0]);
//
//        $temperatureObject = $sensorTypeObjectAfter->getTempObject();
//        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
//        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
//        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());
//
//        $humidityObject = $sensorTypeObjectAfter->getHumidObject();
//        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
//        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
//        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());
//
//        self::assertNotEquals($formData['cardColour'], $cardViewObject->getCardColourID()->getColourID());
//        self::assertNotEquals($formData['cardIcon'], $cardViewObject->getCardIconID()->getIconID());
//        self::assertNotEquals($formData['cardViewState'], $cardViewObject->getCardStateID()->getCardstateID());
//
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//    // Latitude
//    public function test_can_not_update_card_view_form_latitude_selections_high_low_data_bmp()
//    {
//        $sensorType = SensorType::BMP_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $sensorTypeObject = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//        $temperatureObject = $sensorTypeObject->getTempObject();
//        $humidObject = $sensorTypeObject->getHumidObject();
//
//        $highReading = 95;
//        $lowReading = -5;
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//
//            'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
//            'temperature-low-reading' => $temperatureObject->getLowReading() +1,
//            'temperature-const-record' => true,
//
//            'humidity-high-reading' => $humidObject->getHighReading() + 1,
//            'humidity-low-reading' => $humidObject->getLowReading() + 1,
//            'humidity-const-record' => true,
//
//            'latitude-high-reading' => $highReading,
//            'latitude-low-reading' => $lowReading,
//            'latitude-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $sensorTypeObjectAfter = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//
//        $highString = "The highest possible latitude is 90 you entered \"%s\"";
//        $lowString = "The lowest possible latitude is 0 you entered \"%s\"";
//
//        self::assertStringContainsString(sprintf($highString, $highReading), $responseData['payload']['errors'][0]);
//        self::assertStringContainsString(sprintf($lowString, $lowReading), $responseData['payload']['errors'][1]);
//
//        $temperatureObject = $sensorTypeObjectAfter->getTempObject();
//        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
//        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
//        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());
//
//        $humidityObject = $sensorTypeObjectAfter->getHumidObject();
//        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
//        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
//        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());
//
//        $latitudeObject = $sensorTypeObjectAfter->getLatitudeObject();
//        self::assertNotEquals($formData['latitude-high-reading'], $latitudeObject->getHighReading());
//        self::assertNotEquals($formData['latitude-low-reading'], $latitudeObject->getLowReading());
//        self::assertNotEquals($formData['latitude-const-record'], $latitudeObject->getConstRecord());
//
//        self::assertNotEquals($formData['cardColour'], $cardViewObject->getCardColourID()->getColourID());
//        self::assertNotEquals($formData['cardIcon'], $cardViewObject->getCardIconID()->getIconID());
//        self::assertNotEquals($formData['cardViewState'], $cardViewObject->getCardStateID()->getCardstateID());
//
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//    // Analog
//
//    public function test_can_not_update_card_view_form_analog_selections_high_low_data_soil()
//    {
//        $sensorType = SensorType::SOIL_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $highReading = 10000;
//        $lowReading = 999;
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//
//            'analog-high-reading' => $highReading,
//            'analog-low-reading' => $lowReading,
//            'analog-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $sensorTypeObjectAfter = $this->entityManager->getRepository('App\Sensors\Entity\GetSensorTypesController\\' . $sensorType)->findOneBy(['sensorNameID' => $sensorObject]);
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//
//        $highString = "Reading for this sensor cannot be over 9999 you entered \"%s\"";
//        $lowString = "Reading for this sensor cannot be under 1000 you entered \"%s\"";
//
//        self::assertStringContainsString(sprintf($highString, $highReading), $responseData['payload']['errors'][0]);
//        self::assertStringContainsString(sprintf($lowString, $lowReading), $responseData['payload']['errors'][1]);
//
//        $analogObject = $sensorTypeObjectAfter->getAnalogObject();
//        self::assertNotEquals($formData['analog-high-reading'], $analogObject->getHighReading());
//        self::assertNotEquals($formData['analog-low-reading'], $analogObject->getLowReading());
//        self::assertNotEquals($formData['analog-const-record'], $analogObject->getConstRecord());
//
//        self::assertNotEquals($formData['cardColour'], $cardViewObject->getCardColourID()->getColourID());
//        self::assertNotEquals($formData['cardIcon'], $cardViewObject->getCardIconID()->getIconID());
//        self::assertNotEquals($formData['cardViewState'], $cardViewObject->getCardStateID()->getCardstateID());
//
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//
//
//    public function test_can_update_card_view_form_latitude_selections_outofrange_data_bmp()
//    {
//        $sensorType = SensorType::BMP_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);
//        $temperatureObject = $bmpSensor->getTempObject();
//        $humidityObject = $bmpSensor->getHumidObject();
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//
//            'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
//            'temperature-low-reading' => $temperatureObject->getLowReading() + 1,
//            'temperature-const-record' => true,
//
//            'humidity-high-reading' => $humidityObject->getHighReading() + 1,
//            'humidity-low-reading' => $humidityObject->getLowReading() + 1,
//            'humidity-const-record' => true,
//
//            'latitude-high-reading' => '100',
//            'latitude-low-reading' => '-5',
//            'latitude-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//        self::assertStringContainsString('The highest possible latitude is 90 you entered "100"', $responseData['payload']['errors'][0]);
//        self::assertStringContainsString('The lowest possible latitude is 0 you entered "-5"', $responseData['payload']['errors'][1]);
//
//        $temperatureObject = $bmpSensor->getTempObject();
//        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
//        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
//        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());
//
//        $humidityObject = $bmpSensor->getHumidObject();
//        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
//        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
//        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());
//
//        $latitudeObject = $bmpSensor->getLatitudeObject();
//        self::assertNotEquals($formData['latitude-high-reading'], $latitudeObject->getHighReading());
//        self::assertNotEquals($formData['latitude-low-reading'], $latitudeObject->getLowReading());
//        self::assertNotEquals($formData['latitude-const-record'], $latitudeObject->getConstRecord());
//
//        self::assertNotEquals($formData['cardColour'], $cardViewObject->getCardColourID()->getColourID());
//        self::assertNotEquals($formData['cardIcon'], $cardViewObject->getCardIconID()->getIconID());
//        self::assertNotEquals($formData['cardViewState'], $cardViewObject->getCardStateID()->getCardstateID());
//
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//    public function test_cannot_add_wrong_const_record_input()
//    {
//        $sensorType = SensorType::BMP_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);
//        $temperatureObject = $bmpSensor->getTempObject();
//        $humidityObject = $bmpSensor->getHumidObject();
//        $latitudeObject = $bmpSensor->getLatitudeObject();
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => '$newColour',
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//
//            'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
//            'temperature-low-reading' => $temperatureObject->getLowReading() + 1,
//            'temperature-const-record' => true,
//
//            'humidity-high-reading' => $humidityObject->getHighReading() + 1,
//            'humidity-low-reading' => $humidityObject->getLowReading() + 1,
//            'humidity-const-record' => true,
//
//            'latitude-high-reading' => $latitudeObject->getHighReading() + 1,
//            'latitude-low-reading' => $latitudeObject->getLowReading() + 1,
//            'latitude-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//        self::assertStringContainsString('This value is not valid.', $responseData['payload']['errors'][0]);
//
//        $temperatureObject = $bmpSensor->getTempObject();
//        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
//        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
//        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());
//
//        $humidityObject = $bmpSensor->getHumidObject();
//        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
//        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
//        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());
//
//        $latitudeObject = $bmpSensor->getLatitudeObject();
//        self::assertNotEquals($formData['latitude-high-reading'], $latitudeObject->getHighReading());
//        self::assertNotEquals($formData['latitude-low-reading'], $latitudeObject->getLowReading());
//        self::assertNotEquals($formData['latitude-const-record'], $latitudeObject->getConstRecord());
//
//        self::assertNotEquals($formData['cardColour'], $cardViewObject->getCardColourID()->getColourID());
//        self::assertNotEquals($formData['cardIcon'], $cardViewObject->getCardIconID()->getIconID());
//        self::assertNotEquals($formData['cardViewState'], $cardViewObject->getCardStateID()->getCardstateID());
//
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//
//    public function test_cannot_add_wrong_card_colour_record_input()
//    {
//        $sensorType = SensorType::BMP_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        while (true) {
//            $randomInt = random_int(0, 10000);
//            $newCardColour = $this->entityManager->getRepository(CardColour::class)->findOneBy(['colourID' => $randomInt]);
//
//            if ($newCardColour === null) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);
//        $temperatureObject = $bmpSensor->getTempObject();
//        $humidityObject = $bmpSensor->getHumidObject();
//        $latitudeObject = $bmpSensor->getLatitudeObject();
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $randomInt,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $newState,
//
//            'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
//            'temperature-low-reading' => $temperatureObject->getLowReading() + 1,
//            'temperature-const-record' => true,
//
//            'humidity-high-reading' => $humidityObject->getHighReading() + 1,
//            'humidity-low-reading' => $humidityObject->getLowReading() + 1,
//            'humidity-const-record' => true,
//
//            'latitude-high-reading' => $latitudeObject->getHighReading() + 1,
//            'latitude-low-reading' => $latitudeObject->getLowReading() + 1,
//            'latitude-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//        self::assertStringContainsString('This value is not valid.', $responseData['payload']['errors'][0]);
//
//        $temperatureObject = $bmpSensor->getTempObject();
//        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
//        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
//        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());
//
//        $humidityObject = $bmpSensor->getHumidObject();
//        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
//        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
//        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());
//
//        $latitudeObject = $bmpSensor->getLatitudeObject();
//        self::assertNotEquals($formData['latitude-high-reading'], $latitudeObject->getHighReading());
//        self::assertNotEquals($formData['latitude-low-reading'], $latitudeObject->getLowReading());
//        self::assertNotEquals($formData['latitude-const-record'], $latitudeObject->getConstRecord());
//
//        self::assertNotEquals($formData['cardColour'], $cardViewObject->getCardColourID()->getColourID());
//        self::assertNotEquals($formData['cardIcon'], $cardViewObject->getCardIconID()->getIconID());
//        self::assertNotEquals($formData['cardViewState'], $cardViewObject->getCardStateID()->getCardstateID());
//
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//
//    public function test_cannot_add_wrong_icon_record_input()
//    {
//        $sensorType = SensorType::BMP_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardStates = $this->entityManager->getRepository(Cardstate::class)->findAll();
//
//        while (true) {
//            $randomInt = random_int(0, 10000);
//            $newCardIcon = $this->entityManager->getRepository(Icons::class)->findOneBy(['iconID' => $randomInt]);
//
//            if ($newCardIcon === null) {
//                break;
//            }
//        }
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardStates as $state) {
//            $newState = $state->getCardstateID();
//
//            if ($newState !== $cardViewObject->getCardStateID()->getCardstateID()) {
//                break;
//            }
//        }
//
//        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);
//        $temperatureObject = $bmpSensor->getTempObject();
//        $humidityObject = $bmpSensor->getHumidObject();
//        $latitudeObject = $bmpSensor->getLatitudeObject();
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $randomInt,
//            'cardViewState' => $newState,
//
//            'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
//            'temperature-low-reading' => $temperatureObject->getLowReading() + 1,
//            'temperature-const-record' => true,
//
//            'humidity-high-reading' => $humidityObject->getHighReading() + 1,
//            'humidity-low-reading' => $humidityObject->getLowReading() + 1,
//            'humidity-const-record' => true,
//
//            'latitude-high-reading' => $latitudeObject->getHighReading() + 1,
//            'latitude-low-reading' => $latitudeObject->getLowReading() + 1,
//            'latitude-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//        self::assertStringContainsString('This value is not valid.', $responseData['payload']['errors'][0]);
//
//        $temperatureObject = $bmpSensor->getTempObject();
//        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
//        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
//        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());
//
//        $humidityObject = $bmpSensor->getHumidObject();
//        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
//        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
//        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());
//
//        $latitudeObject = $bmpSensor->getLatitudeObject();
//        self::assertNotEquals($formData['latitude-high-reading'], $latitudeObject->getHighReading());
//        self::assertNotEquals($formData['latitude-low-reading'], $latitudeObject->getLowReading());
//        self::assertNotEquals($formData['latitude-const-record'], $latitudeObject->getConstRecord());
//
//        self::assertNotEquals($formData['cardColour'], $cardViewObject->getCardColourID()->getColourID());
//        self::assertNotEquals($formData['cardIcon'], $cardViewObject->getCardIconID()->getIconID());
//        self::assertNotEquals($formData['cardViewState'], $cardViewObject->getCardStateID()->getCardstateID());
//
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//
//    public function test_cannot_add_wrong_card_view_state()
//    {
//        $sensorType = SensorType::BMP_SENSOR;
//
//        $sensorTypeObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
//
//        $sensorObject = $this->entityManager->getRepository(Sensors::class)->findOneBy(['createdBy' => $this->testUser, 'sensorTypeID' => $sensorTypeObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $cardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
//        $cardIcons = $this->entityManager->getRepository(Icons::class)->findAll();
//
//        while (true) {
//            $randomInt = random_int(0, 10000);
//            $newCardState = $this->entityManager->getRepository(Cardstate::class)->findOneBy(['cardStateID' => $randomInt]);
//
//            if ($newCardState === null) {
//                break;
//            }
//        }
//
//        foreach ($cardColours as $colour) {
//            $newColour = $colour->getColourID();
//
//            if ($newColour !== $cardViewObject->getCardColourID()->getColourID()) {
//                break;
//            }
//        }
//
//        foreach ($cardIcons as $icon) {
//            $newIcon = $icon->getIconID();
//
//            if ($newIcon !== $cardViewObject->getCardIconID()->getIconID()) {
//                break;
//            }
//        }
//
//        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);
//        $temperatureObject = $bmpSensor->getTempObject();
//        $humidityObject = $bmpSensor->getHumidObject();
//        $latitudeObject = $bmpSensor->getLatitudeObject();
//
//        $formData = [
//            'cardViewID' => $cardViewObject->getCardViewID(),
//            'cardColour' => $newColour,
//            'cardIcon' => $newIcon,
//            'cardViewState' => $randomInt,
//
//            'temperature-high-reading' => $temperatureObject->getHighReading() + 1,
//            'temperature-low-reading' => $temperatureObject->getLowReading() + 1,
//            'temperature-const-record' => true,
//
//            'humidity-high-reading' => $humidityObject->getHighReading() + 1,
//            'humidity-low-reading' => $humidityObject->getLowReading() + 1,
//            'humidity-const-record' => true,
//
//            'latitude-high-reading' => $latitudeObject->getHighReading() + 1,
//            'latitude-low-reading' => $latitudeObject->getLowReading() + 1,
//            'latitude-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorObject]);
//
//        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['userID' => $this->testUser, 'sensorNameID' => $sensorObject]);
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//        self::assertStringContainsString('This value is not valid.', $responseData['payload']['errors'][0]);
//
//        $temperatureObject = $bmpSensor->getTempObject();
//        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
//        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
//        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());
//
//        $humidityObject = $bmpSensor->getHumidObject();
//        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
//        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
//        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());
//
//        $latitudeObject = $bmpSensor->getLatitudeObject();
//        self::assertNotEquals($formData['latitude-high-reading'], $latitudeObject->getHighReading());
//        self::assertNotEquals($formData['latitude-low-reading'], $latitudeObject->getLowReading());
//        self::assertNotEquals($formData['latitude-const-record'], $latitudeObject->getConstRecord());
//
//        self::assertNotEquals($formData['cardColour'], $cardViewObject->getCardColourID()->getColourID());
//        self::assertNotEquals($formData['cardIcon'], $cardViewObject->getCardIconID()->getIconID());
//        self::assertNotEquals($formData['cardViewState'], $cardViewObject->getCardStateID()->getCardstateID());
//
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//
//    public function test_cannot_adjust_none_existant_card_view()
//    {
//        $cardColour = $this->entityManager->getRepository(CardColour::class)->findAll()[0]->getColourID();
//        $cardIcon = $this->entityManager->getRepository(Icons::class)->findAll()[0]->getIconID();
//        $cardState = $this->entityManager->getRepository(Cardstate::class)->findAll()[0]->getCardstateID();
//
//        while (true) {
//            $randomInt = random_int(1, 1000);
//
//            $card = $this->entityManager->getRepository(CardView::class)->findOneBy(['cardViewID' => $randomInt]);
//
//            if ($card === null) {
//                break;
//            }
//        }
//
//        $formData = [
//            'cardViewID' => $randomInt,
//            'cardColour' => $cardColour,
//            'cardIcon' => $cardIcon,
//            'cardViewState' => $cardState,
//
//            'temperature-high-reading' => '40',
//            'temperature-low-reading' => '10',
//            'temperature-const-record' => true,
//
//            'humidity-high-reading' => '10',
//            'humidity-low-reading' => '60',
//            'humidity-const-record' => true,
//
//            'latitude-high-reading' => '10',
//            'latitude-low-reading' => '30',
//            'latitude-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//    public function test_cannot_adjust_not_owened_card_view()
//    {
//        $cardColour = $this->entityManager->getRepository(CardColour::class)->findAll()[0]->getColourID();
//        $cardIcon = $this->entityManager->getRepository(Icons::class)->findAll()[0]->getIconID();
//        $cardState = $this->entityManager->getRepository(Cardstate::class)->findAll()[0]->getCardstateID();
//        $cards = $this->entityManager->getRepository(CardView::class)->findAll();
//
//        foreach ($cards as $card) {
//            /**
//             * @var CardView $card
//             */
//            if ($card->getSensorNameID()->getSensorName() === 'Bmp0' && $card->getUserID()->getUserID() !== $this->testUser->getUserID()) {
//                $cardNotOwnedByUser = $card;
//                break;
//            }
//        }
//
//        $formData = [
//            'cardViewID' => $cardNotOwnedByUser->getCardViewID(),
//            'cardColour' => $cardColour,
//            'cardIcon' => $cardIcon,
//            'cardViewState' => $cardState,
//
//            'temperature-high-reading' => '40',
//            'temperature-low-reading' => '15',
//            'temperature-const-record' => true,
//
//            'humidity-high-reading' => '10',
//            'humidity-low-reading' => '60',
//            'humidity-const-record' => true,
//
//            'latitude-high-reading' => '15',
//            'latitude-low-reading' => '5',
//            'latitude-const-record' => true,
//        ];
//
//        $this->client->request(
//            'POST',
//            self::API_UPDATE_CARD_VIEW_FORM,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//        );
//
//        $sensorTypeObjectAfter = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $card->getSensorNameID()->getSensorNameID()]);
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//
//        $temperatureObject = $sensorTypeObjectAfter->getTempObject();
//        self::assertNotEquals($formData['temperature-high-reading'], $temperatureObject->getHighReading());
//        self::assertNotEquals($formData['temperature-low-reading'], $temperatureObject->getLowReading());
//        self::assertNotEquals($formData['temperature-const-record'], $temperatureObject->getConstRecord());
//
//        $humidityObject = $sensorTypeObjectAfter->getHumidObject();
//        self::assertNotEquals($formData['humidity-high-reading'], $humidityObject->getHighReading());
//        self::assertNotEquals($formData['humidity-low-reading'], $humidityObject->getLowReading());
//        self::assertNotEquals($formData['humidity-const-record'], $humidityObject->getConstRecord());
//
//        $latitudeObject = $sensorTypeObjectAfter->getLatitudeObject();
//        self::assertNotEquals($formData['latitude-high-reading'], $latitudeObject->getHighReading());
//        self::assertNotEquals($formData['latitude-low-reading'], $latitudeObject->getLowReading());
//        self::assertNotEquals($formData['latitude-const-record'], $latitudeObject->getConstRecord());
//
//        self::assertStringContainsString('You Are Not Authorised To Be Here', $responseData['title']);
//        self::assertEquals(HTTPStatusCodes::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
//    }


    // Route Authentication Test
//    public function test_getting_card_data_wrong_token()
//    {
//        $this->client->request(
//            'GET',
//            self::API_CARD_DATA_RETURN_CARD_DTO_ROUTE,
//            ['view' => 'index'],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken . 'wrong'],
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
//
//        self::assertEquals('Invalid JWT Token', $responseData['message']);
//        self::assertEquals(HTTPStatusCodes::HTTP_UNAUTHORISED, $this->client->getResponse()->getStatusCode());
////        dd($this->client->getResponse());
//    }
//
//    public function test_getting_card_state_view_form_wrong_token()
//    {
//        $cardView = $this->entityManager->getRepository(CardView::class)->findBy(['userID' => $this->testUser])[0];
//
//        $this->client->request(
//            'GET',
//            self::API_CARD_VIEW_FORM_DTO_URL,
//            ['cardViewID' => $cardView->getCardViewID()],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken. 'wrong'],
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
//
////        dd($this->client->getResponse());
//        self::assertEquals('Invalid JWT Token', $responseData['message']);
//        self::assertEquals(HTTPStatusCodes::HTTP_UNAUTHORISED, $this->client->getResponse()->getStatusCode());
//    }
//
//    public function test_getting_update_card_view_wrong_token()
//    {
//        $this->client->request(
//            Request::METHOD_PUT,
//            self::API_UPDATE_CARD_VIEW_FORM,
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken .'wrong'],
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
//
//        self::assertEquals('Invalid JWT Token', $responseData['message']);
//        self::assertEquals(HTTPStatusCodes::HTTP_UNAUTHORISED, $this->client->getResponse()->getStatusCode());
//    }
}
