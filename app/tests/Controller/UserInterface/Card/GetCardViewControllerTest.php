<?php

namespace App\Tests\Controller\UserInterface\Card;

use App\Controller\Sensor\ReadingTypeControllers\UpdateSensorBoundaryReadingsController;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Device\Devices;
use App\Entity\Sensor\AbstractSensorType;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\ReadingTypes;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\Sensor;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Entity\UserInterface\Card\CardView;
use App\Factories\Sensor\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\Repository\Device\ORM\DeviceRepository;
use App\Repository\User\ORM\GroupRepository;
use App\Services\API\APIErrorMessages;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetCardViewControllerTest extends WebTestCase
{
    use TestLoginTrait;

    public const CARD_VIEW_URL = '/HomeApp/api/user/cards/%s';

    public const DEVICE_CARD_VIEW_URL = self::CARD_VIEW_URL . '/%d';

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;

    private User $regularUserOne;

    private User $adminUserOne;

    private GroupRepository $groupNameRepository;

    private DeviceRepository $deviceRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->sensorTypeQueryFactory = static::getContainer()
            ->get(SensorTypeQueryFactory::class);

        $this->userToken = $this->setUserToken($this->client);

        $this->groupNameRepository = $this->entityManager->getRepository(Group::class);
        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);

        $this->regularUserOne = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $this->adminUserOne = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
    }

    public function test_admin_getting_all_card_data(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::CARD_VIEW_URL, 'index'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL, $responseData['title']);
        self::assertIsArray($responseData['payload']);
        self::assertGreaterThan(1, count($responseData['payload']));

        $cardViewRepository = $this->entityManager->getRepository(\App\Entity\UserInterface\Card\CardView::class);
        $sensorRepository = $this->entityManager->getRepository(Sensor::class);

        $temperature = false;
        $humidity = false;
        $latitude = false;
        $motion = false;
        $relay = false;
        $analog = false;

//        dd($responseData['payload']);
        foreach ($responseData['payload'] as $payload) {
            /** @var \App\Entity\UserInterface\Card\CardView $cardViewObject */
            $cardViewObject = $cardViewRepository->findOneBy(['cardViewID' => $payload['cardViewID']]);

            self::assertEquals($cardViewObject->getCardViewID(), $payload['cardViewID']);
            self::assertEquals($cardViewObject->getSensor()->getSensorName(), $payload['sensorName']);
            self::assertEquals($cardViewObject->getSensor()->getSensorTypeObject()::getReadingTypeName(), $payload['sensorType']);
            self::assertEquals($cardViewObject->getSensor()->getDevice()->getRoomObject()->getRoom(), $payload['sensorRoom']);
            self::assertEquals($cardViewObject->getCardIconID()->getIconName(), $payload['cardIcon']);
            self::assertEquals($cardViewObject->getCardColourID()->getColour(), $payload['cardColour']);

            $readingTypeQueryDTOs = $this->sensorTypeQueryFactory
                ->getSensorTypeQueryDTOBuilder($payload['sensorType'])
                ->buildSensorReadingTypes();

            /** @var Sensor[] $cardSensorReadingTypeObjects */
            $cardSensorReadingTypeObjects = $sensorRepository->findSensorTypeAndReadingTypeObjectsForSensor(
                $cardViewObject->getSensor()->getDevice()->getDeviceID(),
                $cardViewObject->getSensor()->getSensorName(),
                null,
                $readingTypeQueryDTOs,
            );
//dd($payload);
            self::assertNotEmpty($cardSensorReadingTypeObjects);
            $sensorDataArrayCount = 0;
//            dd($cardSensorReadingTypeObjects, $cardViewObject);
            foreach ($cardSensorReadingTypeObjects as $key => $cardSensorReadingTypeObject) {
//                dd($key);
                if ($cardSensorReadingTypeObject instanceof StandardReadingSensorInterface) {
                    if ($cardSensorReadingTypeObject instanceof Temperature) {
                        $temperature = true;
                        self::assertEquals(
                            Temperature::READING_TYPE,
                            $payload['sensorData'][$key]['readingType']
                        );
                    }
                    if ($cardSensorReadingTypeObject instanceof Humidity) {
                        $humidity = true;
                        self::assertEquals(
                            Humidity::READING_TYPE,
                            $payload['sensorData'][$sensorDataArrayCount]['readingType']
                        );
                    }
                    if ($cardSensorReadingTypeObject instanceof Analog) {
                        $analog = true;
                        self::assertEquals(
                            Analog::READING_TYPE,
                            $payload['sensorData'][$sensorDataArrayCount]['readingType']
                        );
                    }
                    if ($cardSensorReadingTypeObject instanceof Latitude) {
                        $latitude = true;
                        self::assertEquals(
                            Latitude::READING_TYPE,
                            $payload['sensorData'][$sensorDataArrayCount]['readingType']
                        );
                    }
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getUpdatedAt()->format('d-m-Y H:i:s'),
                        $payload['sensorData'][$sensorDataArrayCount]['updatedAt']
                    );
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getCurrentReading(),
                        $payload['sensorData'][$sensorDataArrayCount]['currentReading']
                    );
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getHighReading(),
                        $payload['sensorData'][$sensorDataArrayCount]['highReading']
                    );
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getLowReading(),
                        $payload['sensorData'][$sensorDataArrayCount]['lowReading']
                    );
                    if (isset($payload['sensorData'][$sensorDataArrayCount]['readingSymbol'])) {
                        self::assertEquals(
                            $cardSensorReadingTypeObject::getReadingSymbol(),
                            $payload['sensorData'][$sensorDataArrayCount]['readingSymbol']
                        );
                    }
                }
                if ($cardSensorReadingTypeObject instanceof BoolReadingSensorInterface) {
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getUpdatedAt()->format('d-m-Y H:i:s'),
                        $payload['sensorData'][$sensorDataArrayCount]['updatedAt']
                    );

                    self::assertEquals(
                        $cardSensorReadingTypeObject->getCurrentReading(),
                        $payload['sensorData'][$sensorDataArrayCount]['currentReading']
                    );

                    self::assertEquals(
                        $cardSensorReadingTypeObject->getExpectedReading(),
                        $payload['sensorData'][$sensorDataArrayCount]['expectedReading']
                    );

                    self::assertEquals(
                        $cardSensorReadingTypeObject->getRequestedReading(),
                        $payload['sensorData'][$sensorDataArrayCount]['requestedReading']
                    );

                    if ($cardSensorReadingTypeObject instanceof Motion) {
                        $motion = true;
                        self::assertEquals(
                            Motion::READING_TYPE,
                            $payload['sensorData'][$sensorDataArrayCount]['readingType']
                        );
                    }

                    if ($cardSensorReadingTypeObject instanceof Relay) {
                        $relay = true;
                        self::assertEquals(
                            Relay::READING_TYPE,
                            $payload['sensorData'][$sensorDataArrayCount]['readingType']
                        );
                    }
                }
                ++$sensorDataArrayCount;
            }
        }

        self::assertTrue($temperature);
        self::assertTrue($humidity);
        self::assertTrue($latitude);
        self::assertTrue($analog);
        self::assertTrue($motion);
        self::assertTrue($relay);
    }

    public function test_regular_user_getting_all_card_data(): void
    {
        $userToken = $this->setUserToken(
            $this->client,
            $this->regularUserOne->getEmail(),
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::CARD_VIEW_URL, 'index'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL, $responseData['title']);
        self::assertIsArray($responseData['payload']);
        self::assertGreaterThan(1, count($responseData['payload']));

        $cardViewRepository = $this->entityManager->getRepository(\App\Entity\UserInterface\Card\CardView::class);
        $sensorRepository = $this->entityManager->getRepository(Sensor::class);

        foreach ($responseData['payload'] as $payload) {
            /** @var \App\Entity\UserInterface\Card\CardView $cardViewObject */
            $cardViewObject = $cardViewRepository->findOneBy(['cardViewID' => $payload['cardViewID']]);

            self::assertEquals($cardViewObject->getCardViewID(), $payload['cardViewID']);
            self::assertEquals($cardViewObject->getSensor()->getSensorName(), $payload['sensorName']);
            self::assertEquals($cardViewObject->getSensor()->getSensorTypeObject()::getReadingTypeName(), $payload['sensorType']);
            self::assertEquals($cardViewObject->getSensor()->getDevice()->getRoomObject()->getRoom(), $payload['sensorRoom']);
            self::assertEquals($cardViewObject->getCardIconID()->getIconName(), $payload['cardIcon']);
            self::assertEquals($cardViewObject->getCardColourID()->getColour(), $payload['cardColour']);

            $readingTypeQueryDTOs = $this->sensorTypeQueryFactory
                ->getSensorTypeQueryDTOBuilder($payload['sensorType'])
                ->buildSensorReadingTypes();

            /** @var Sensor[] $cardSensorReadingTypeObjects */
            $cardSensorReadingTypeObjects = $sensorRepository->findSensorTypeAndReadingTypeObjectsForSensor(
                $cardViewObject->getSensor()->getDevice()->getDeviceID(),
                $cardViewObject->getSensor()->getSensorName(),
                null,
                $readingTypeQueryDTOs,
            );

            self::assertNotEmpty($cardSensorReadingTypeObjects);
            $sensorDataArrayCount = 0;
            foreach ($cardSensorReadingTypeObjects as $cardSensorReadingTypeObject) {
                if ($cardSensorReadingTypeObject instanceof StandardReadingSensorInterface) {
                    if ($cardSensorReadingTypeObject instanceof Temperature) {
                        self::assertEquals(
                            Temperature::READING_TYPE,
                            $payload['sensorData'][$sensorDataArrayCount]['readingType']
                        );
                    }
                    if ($cardSensorReadingTypeObject instanceof Humidity) {
                        self::assertEquals(
                            Humidity::READING_TYPE,
                            $payload['sensorData'][$sensorDataArrayCount]['readingType']
                        );
                    }
                    if ($cardSensorReadingTypeObject instanceof Analog) {
                        self::assertEquals(
                            Analog::READING_TYPE,
                            $payload['sensorData'][$sensorDataArrayCount]['readingType']
                        );
                    }
                    if ($cardSensorReadingTypeObject instanceof Latitude) {
                        self::assertEquals(
                            Latitude::READING_TYPE,
                            $payload['sensorData'][$sensorDataArrayCount]['readingType']
                        );
                    }
                    self::assertEquals(
//                        $cardSensorReadingTypeObject->getUpdatedAt()->modify('+1second')->format('d-m-Y H:i:s'),
                        $cardSensorReadingTypeObject->getUpdatedAt()->format('d-m-Y H:i:s'),
                        $payload['sensorData'][$sensorDataArrayCount]['updatedAt']
                    );
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getCurrentReading(),
                        $payload['sensorData'][$sensorDataArrayCount]['currentReading']
                    );
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getHighReading(),
                        $payload['sensorData'][$sensorDataArrayCount]['highReading']
                    );
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getLowReading(),
                        $payload['sensorData'][$sensorDataArrayCount]['lowReading']
                    );
                    if (isset($payload['sensorData'][$sensorDataArrayCount]['readingSymbol'])) {
                        self::assertEquals(
                            $cardSensorReadingTypeObject::getReadingSymbol(),
                            $payload['sensorData'][$sensorDataArrayCount]['readingSymbol']
                        );
                    }
                }
                if ($cardSensorReadingTypeObject instanceof BoolReadingSensorInterface) {
                    if ($cardSensorReadingTypeObject instanceof Motion) {
                        self::assertEquals(
                            Motion::READING_TYPE,
                            $payload['sensorData'][$sensorDataArrayCount]['readingType']
                        );
                    }
                    if ($cardSensorReadingTypeObject instanceof Relay) {
                        self::assertEquals(
                            Relay::READING_TYPE,
                            $payload['sensorData'][$sensorDataArrayCount]['readingType']
                        );
                    }
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getUpdatedAt()->format('d-m-Y H:i:s'),
                        $payload['sensorData'][$sensorDataArrayCount]['updatedAt']
                    );
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getCurrentReading(),
                        $payload['sensorData'][$sensorDataArrayCount]['currentReading']
                    );
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getExpectedReading(),
                        $payload['sensorData'][$sensorDataArrayCount]['expectedReading']
                    );
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getRequestedReading(),
                        $payload['sensorData'][$sensorDataArrayCount]['requestedReading']
                    );
                }
                ++$sensorDataArrayCount;
            }
        }
    }

    public function test_getting_device_card_data_device_not_apart_of(): void
    {
        $userGroupsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->regularUserOne);

        $device = null;
        foreach ($userGroupsNotApartOf as $userGroupNotApartOf) {
            $device = $this->deviceRepository->findOneBy(['groupID' => $userGroupNotApartOf]);
            if ($device !== null) {
                break;
            }
        }

        $userToken = $this->setUserToken(
            $this->client,
            $this->regularUserOne->getEmail(),
            UserDataFixtures::REGULAR_PASSWORD
        );
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::DEVICE_CARD_VIEW_URL, 'device', $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$userToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $title = $responseData['title'];
        self::assertEquals(UpdateSensorBoundaryReadingsController::NOT_AUTHORIZED_TO_BE_HERE, $title);

        $errors = $responseData['errors'];
        self::assertEquals([APIErrorMessages::ACCESS_DENIED], $errors);
    }

    /**
     * @dataProvider noReadingTypeReturnedDataProvider
     */
    public function test_no_reading_type_returned_cards_get_returned(array $readingTypes): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::CARD_VIEW_URL, 'index'),
            ['reading-types' => $readingTypes],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL, $responseData['title']);
        self::assertIsArray($responseData['payload']);
        self::assertGreaterThan(1, count($responseData['payload']));

        $cardViewRepository = $this->entityManager->getRepository(CardView::class);
        $sensorRepository = $this->entityManager->getRepository(Sensor::class);

        foreach ($responseData['payload'] as $payload) {
            /** @var \App\Entity\UserInterface\Card\CardView $cardViewObject */
            $cardViewObject = $cardViewRepository->findOneBy(['cardViewID' => $payload['cardViewID']]);

            self::assertEquals($cardViewObject->getCardViewID(), $payload['cardViewID']);
            self::assertEquals($cardViewObject->getSensor()->getSensorName(), $payload['sensorName']);
            self::assertEquals($cardViewObject->getSensor()->getSensorTypeObject()::getReadingTypeName(), $payload['sensorType']);
            self::assertEquals($cardViewObject->getSensor()->getDevice()->getRoomObject()->getRoom(), $payload['sensorRoom']);
            self::assertEquals($cardViewObject->getCardIconID()->getIconName(), $payload['cardIcon']);
            self::assertEquals($cardViewObject->getCardColourID()->getColour(), $payload['cardColour']);

            $readingTypeQueryDTOs = $this->sensorTypeQueryFactory
                ->getSensorTypeQueryDTOBuilder($payload['sensorType'])
                ->buildSensorReadingTypes();

            $arrayPlace = 0;
            foreach ($readingTypes as $readingType) {
                foreach ($readingTypeQueryDTOs as $readingTypeQueryDTO) {
                    /** @var \App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO $readingTypeQueryDTO */
                    if ($readingTypeQueryDTO->getAlias() === ReadingTypes::SENSOR_READING_TYPE_DATA[$readingType]['alias']) {
                        unset($readingTypeQueryDTOs[$arrayPlace]);
                    }
                }
                ++$arrayPlace;
            }

            $cardSensorReadingTypeObjects = $sensorRepository->findSensorTypeAndReadingTypeObjectsForSensor(
                $cardViewObject->getSensor()->getDevice()->getDeviceID(),
                $cardViewObject->getSensor()->getSensorName(),
                null,
                $readingTypeQueryDTOs,
            );

            $sensorDataArrayCount = 0;
            foreach ($readingTypes as $readingType) {
                self::assertNotEquals($readingType, $payload['sensorData'][$sensorDataArrayCount]['readingType']);
            }
            foreach ($cardSensorReadingTypeObjects as $cardSensorReadingTypeObject) {
                if ($cardSensorReadingTypeObject instanceof StandardReadingSensorInterface) {
                    if ($cardSensorReadingTypeObject::getReadingTypeName() === $payload['sensorData'][$sensorDataArrayCount]['readingType']) {
                        self::assertEquals(
                            $cardSensorReadingTypeObject->getUpdatedAt()->format('d-m-Y H:i:s'),
                            $payload['sensorData'][$sensorDataArrayCount]['updatedAt']
                        );
                        self::assertEquals(
                            $cardSensorReadingTypeObject->getCurrentReading(),
                            $payload['sensorData'][$sensorDataArrayCount]['currentReading']
                        );
                        self::assertEquals(
                            $cardSensorReadingTypeObject->getHighReading(),
                            $payload['sensorData'][$sensorDataArrayCount]['highReading']
                        );
                        self::assertEquals(
                            $cardSensorReadingTypeObject->getLowReading(),
                            $payload['sensorData'][$sensorDataArrayCount]['lowReading']
                        );
                        if (isset($payload['sensorData'][$sensorDataArrayCount]['readingSymbol'])) {
                            self::assertEquals(
                                $cardSensorReadingTypeObject::getReadingSymbol(),
                                $payload['sensorData'][$sensorDataArrayCount]['readingSymbol']
                            );
                        }
                    }
                    ++$sensorDataArrayCount;
                    continue;
                }
                if ($cardSensorReadingTypeObject instanceof BoolReadingSensorInterface) {
                    if ($cardSensorReadingTypeObject::getReadingTypeName() === $payload['sensorData'][$sensorDataArrayCount]['readingType']) {
                        self::assertEquals(
                            $cardSensorReadingTypeObject->getUpdatedAt()->format('d-m-Y H:i:s'),
                            $payload['sensorData'][$sensorDataArrayCount]['updatedAt']
                        );
                        self::assertEquals(
                            $cardSensorReadingTypeObject->getCurrentReading(),
                            $payload['sensorData'][$sensorDataArrayCount]['currentReading']
                        );
                        self::assertEquals(
                            $cardSensorReadingTypeObject->getExpectedReading(),
                            $payload['sensorData'][$sensorDataArrayCount]['expectedReading']
                        );
                        self::assertEquals(
                            $cardSensorReadingTypeObject->getRequestedReading(),
                            $payload['sensorData'][$sensorDataArrayCount]['requestedReading']
                        );
                    }
                    ++$sensorDataArrayCount;
                    continue;
                }

                ++$sensorDataArrayCount;
            }
        }
    }

    public function noReadingTypeReturnedDataProvider(): Generator
    {
        yield [
            [
                Temperature::READING_TYPE,
            ]
        ];
        yield [
            [
                Humidity::READING_TYPE,
            ]
        ];
        yield [
            [
                Latitude::READING_TYPE,
            ]
        ];
        yield [
            [
                Analog::READING_TYPE,
            ]
        ];
        yield [
            [
                Motion::READING_TYPE,
            ]
        ];
        yield [
            [
                Relay::READING_TYPE,
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Humidity::READING_TYPE,
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Latitude::READING_TYPE,
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Analog::READING_TYPE,
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Motion::READING_TYPE,
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Relay::READING_TYPE,
            ]
        ];

        yield [
            [
                Humidity::READING_TYPE,
                Latitude::READING_TYPE,
            ]
        ];

        yield [
            [
                Humidity::READING_TYPE,
                Analog::READING_TYPE,
            ]
        ];

        yield [
            [
                Humidity::READING_TYPE,
                Motion::READING_TYPE,
            ]
        ];

        yield [
            [
                Humidity::READING_TYPE,
                Relay::READING_TYPE,
            ]
        ];

        yield [
            [
                Latitude::READING_TYPE,
                Analog::READING_TYPE,
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Humidity::READING_TYPE,
                Latitude::READING_TYPE
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Humidity::READING_TYPE,
                Analog::READING_TYPE
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Humidity::READING_TYPE,
                Motion::READING_TYPE
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Humidity::READING_TYPE,
                Relay::READING_TYPE
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Latitude::READING_TYPE,
                Analog::READING_TYPE
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Latitude::READING_TYPE,
                Motion::READING_TYPE
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Latitude::READING_TYPE,
                Relay::READING_TYPE
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Analog::READING_TYPE,
                Motion::READING_TYPE
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Analog::READING_TYPE,
                Relay::READING_TYPE
            ]
        ];

        yield [
            [
                Humidity::READING_TYPE,
                Latitude::READING_TYPE,
                Analog::READING_TYPE
            ]
        ];

        yield [
            [
                Humidity::READING_TYPE,
                Latitude::READING_TYPE,
                Motion::READING_TYPE
            ]
        ];

        yield [
            [
                Humidity::READING_TYPE,
                Latitude::READING_TYPE,
                Relay::READING_TYPE
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Humidity::READING_TYPE,
                Latitude::READING_TYPE,
            ]
        ];

        yield [
            [
                Humidity::READING_TYPE,
                Latitude::READING_TYPE,
            ]
        ];

        yield [
            [
                Humidity::READING_TYPE,
                Analog::READING_TYPE
            ]
        ];

        yield [
            [
                Latitude::READING_TYPE,
                Analog::READING_TYPE
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Humidity::READING_TYPE,
                Latitude::READING_TYPE,
                Analog::READING_TYPE
            ]
        ];
    }

    public function test_sending_all_reading_types_returns_error(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::CARD_VIEW_URL, 'index'),
            ['reading-types' => ReadingTypes::ALL_READING_TYPES],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $requestResponse = $this->client->getResponse();

        $content = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $errors = $content['errors'];

        self::assertEquals('All reading types selected, please unselect some', $errors[0]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $requestResponse->getStatusCode());
    }

    public function test_sending_all_sensor_types_returns_error(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::CARD_VIEW_URL, 'index'),
            ['sensor-types' => AbstractSensorType::ALL_SENSOR_TYPES],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $requestResponse = $this->client->getResponse();

        $content = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $errors = $content['errors'];

        self::assertEquals('All sensor types selected, please unselect some', $errors[0]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $requestResponse->getStatusCode());

    }
    
//@TODO add tests for device and room
    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;

        parent::tearDown();
    }
}
