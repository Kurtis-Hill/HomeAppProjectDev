<?php

namespace App\Tests\Sensors\Controller\TriggerControllers;

use App\Common\Entity\Operator;
use App\Common\Entity\TriggerType;
use App\Common\Repository\OperatorRepository;
use App\Common\Repository\TriggerTypeRepository;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepository;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\ORM\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Sensors\Controller\TriggerControllers\AddSensorTriggerController;
use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTrigger;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Repository\ReadingType\ORM\BaseSensorReadingTypeRepository;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\Repository\SensorTriggerRepository;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Group;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use JsonException;
use Proxies\__CG__\App\Sensors\Entity\SensorType;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddSensorTriggerControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const ADD_NEW_SENSOR_TRIGGER_URL = '/HomeApp/api/user/sensor-trigger/form/add';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private DeviceRepository $deviceRepository;

    private SensorRepositoryInterface $sensorRepository;

    private GroupRepository $groupRepository;

    private BaseSensorReadingTypeRepository $baseSensorReadingTypeRepository;

    private OperatorRepository $operatorRepository;

    private TriggerTypeRepository $triggerTypeRepository;

    private SensorTriggerRepository $sensorTriggerRepository;

    private ?string $userToken = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
        $this->groupRepository = $this->entityManager->getRepository(Group::class);
        $this->baseSensorReadingTypeRepository = $this->entityManager->getRepository(BaseSensorReadingType::class);
        $this->operatorRepository = $this->entityManager->getRepository(Operator::class);
        $this->triggerTypeRepository = $this->entityManager->getRepository(TriggerType::class);
        $this->sensorTriggerRepository = $this->entityManager->getRepository(SensorTrigger::class);

        try {
            $this->device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['name']]);
            $this->userToken = $this->setUserToken($this->client);
        } catch (JsonException $e) {
            error_log($e);
        }
    }

    /**
     * @dataProvider invalidRequestDataProvider
     */
    public function test_sending_invalid_request_data(
        mixed $operator,
        mixed $triggerType,
        mixed $baseReadingTypeThatTriggers,
        mixed $baseReadingTypeThatIsTriggered,
        mixed $days,
        mixed $valueThatTriggers,
        mixed $startTime,
        mixed $endTime,
        mixed $errorMessage
    ): void {
        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_TRIGGER_URL,
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(
                [
                    'operator' => $operator,
                    'triggerType' => $triggerType,
                    'baseReadingTypeThatTriggers' => $baseReadingTypeThatTriggers,
                    'baseReadingTypeThatIsTriggered' => $baseReadingTypeThatIsTriggered,
                    'days' => $days,
                    'valueThatTriggers' => $valueThatTriggers,
                    'startTime' => $startTime,
                    'endTime' => $endTime,
                ],
                JSON_THROW_ON_ERROR
            )
        );

        $response = $this->client->getResponse();
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertJson($response->getContent());

        $payload = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals($errorMessage, $payload['errors']);
    }

    public function invalidRequestDataProvider(): Generator
    {
        yield [
            'operator' => 'invalid operator',
            'triggerType' => 1,
            'baseReadingTypeThatTriggers' => 1,
            'baseReadingTypeThatIsTriggered' => 1,
            'days' => ['monday'],
            'valueThatTriggers' => 1.1,
            'startTime' => 1000,
            'endTime' => 2000,
            'errorMessage' => ['operator must be an int you have provided "invalid operator"']
        ];

        yield [
            'operator' => 1,
            'triggerType' => 'invalid trigger type',
            'baseReadingTypeThatTriggers' => 1,
            'baseReadingTypeThatIsTriggered' => 1,
            'days' => ['monday'],
            'valueThatTriggers' => 1.1,
            'startTime' => 1000,
            'endTime' => 2000,
            'errorMessage' => ['trigger type must be an int you have provided "invalid trigger type"']
        ];

        yield [
            'operator' => 1,
            'triggerType' => 1,
            'baseReadingTypeThatTriggers' => 'invalid base reading type that triggers',
            'baseReadingTypeThatIsTriggered' => 1,
            'days' => ['monday'],
            'valueThatTriggers' => 1.1,
            'startTime' => 1000,
            'endTime' => 2000,
            'errorMessage' => ['base reading type that triggers must be an int|null you have provided "invalid base reading type that triggers"']
        ];

        yield [
            'operator' => 1,
            'triggerType' => 1,
            'baseReadingTypeThatTriggers' => 1,
            'baseReadingTypeThatIsTriggered' => 'invalid reading type that is triggered',
            'days' => ['monday'],
            'valueThatTriggers' => 1.1,
            'startTime' => 1000,
            'endTime' => 2000,
            'errorMessage' => ['base reading type that is triggered must be an int|null you have provided "invalid reading type that is triggered"']
        ];

        yield [
            'operator' => 1,
            'triggerType' => 1,
            'baseReadingTypeThatTriggers' => 1,
            'baseReadingTypeThatIsTriggered' => 1,
            'days' => ['invalid day'],
            'valueThatTriggers' => 1.1,
            'startTime' => 1000,
            'endTime' => 2000,
            'errorMessage' => ['Days must be of "monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"']
        ];

        yield [
            'operator' => 1,
            'triggerType' => 1,
            'baseReadingTypeThatTriggers' => 1,
            'baseReadingTypeThatIsTriggered' => 1,
            'days' => ['monday'],
            'valueThatTriggers' => 'invalid value that triggers',
            'startTime' => 1000,
            'endTime' => 2000,
            'errorMessage' => ['value that triggers must be an bool|float you have provided "invalid value that triggers"']
        ];

        yield [
            'operator' => 1,
            'triggerType' => 1,
            'baseReadingTypeThatTriggers' => 1,
            'baseReadingTypeThatIsTriggered' => 1,
            'days' => ['monday'],
            'valueThatTriggers' => true,
            'startTime' => 'invalid start time',
            'endTime' => 2000,
            'errorMessage' => ['Start time cannot be greater than end time', 'start time must be an int you have provided "invalid start time"']
        ];

        yield [
            'operator' => 1,
            'triggerType' => 1,
            'baseReadingTypeThatTriggers' => 1,
            'baseReadingTypeThatIsTriggered' => 1,
            'days' => ['monday'],
            'valueThatTriggers' => 1.1,
            'startTime' => 1000,
            'endTime' => 'invalid end time',
            'errorMessage' => ['end time must be an int you have provided "invalid end time"']
        ];

        yield [
            'operator' => 1,
            'triggerType' => 1,
            'baseReadingTypeThatTriggers' => 1,
            'baseReadingTypeThatIsTriggered' => 1,
            'days' => ['monday'],
            'valueThatTriggers' => 1.1,
            'startTime' => 100,
            'endTime' => 2000,
            'errorMessage' => ['Trigger type must be in 24 hour format']
        ];

        yield [
            'operator' => 1,
            'triggerType' => 1,
            'baseReadingTypeThatTriggers' => 1,
            'baseReadingTypeThatIsTriggered' => 1,
            'days' => ['monday'],
            'valueThatTriggers' => 1.1,
            'startTime' => 1000,
            'endTime' => 200,
            'errorMessage' => ['Start time cannot be greater than end time', 'Trigger type must be in 24 hour format']
        ];
    }

    public function test_user_cannot_add_trigger_for_sensor_not_apart_of(): void
    {
        /** @var User $regularUser */
        $regularUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        $groupsUserIsNotApartOf = $this->groupRepository->findGroupsUserIsNotApartOf($regularUser);

        $devicesNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);
        $deviceNotApartOf = $devicesNotApartOf[0];

        $sensorsNotApartOf = $this->sensorRepository->findBy(['deviceID' => $deviceNotApartOf]);
        $sensorNotApartOf = $sensorsNotApartOf[0];

        $baseReadingTypesNotApartOf = $this->baseSensorReadingTypeRepository->findBy(['sensor' => $sensorNotApartOf]);
        $baseReadingTypeNotApartOf = $baseReadingTypesNotApartOf[0];

        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_TWO, UserDataFixtures::REGULAR_PASSWORD);

        $operator = $this->operatorRepository->findAll()[0];
        $triggerType = $this->triggerTypeRepository->findAll()[0];

        $groupsUserIsApartOf = $regularUser->getAssociatedGroupIDs();

        $devicesApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);
        $deviceApartOf = $devicesApartOf[0];

        $sensorsApartOf = $this->sensorRepository->findBy(['deviceID' => $deviceApartOf]);
        $sensorApartOf = $sensorsApartOf[0];

        $baseReadingTypesApartOf = $this->baseSensorReadingTypeRepository->findBy(['sensor' => $sensorApartOf]);
        $baseReadingTypeApartOf = $baseReadingTypesApartOf[0];

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_TRIGGER_URL,
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $userToken,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(
                [
                    'operator' => $operator->getOperatorID(),
                    'triggerType' => $triggerType->getTriggerTypeID(),
                    'baseReadingTypeThatTriggers' => $baseReadingTypeApartOf->getBaseReadingTypeID(),
                    'baseReadingTypeThatIsTriggered' => $baseReadingTypeNotApartOf->getBaseReadingTypeID(),
                    'days' => ['monday'],
                    'valueThatTriggers' => 1.1,
                    'startTime' => 1000,
                    'endTime' => 2000,
                ],
                JSON_THROW_ON_ERROR
            )
        );

        $response = $this->client->getResponse();
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('errors', $responseData);

        $title = $responseData['title'];
        self::assertEquals(AddSensorTriggerController::NOT_AUTHORIZED_TO_BE_HERE, $title);
    }

    public function test_user_cannot_add_trigger_from_sensor_not_apart_of(): void
    {
        /** @var User $regularUser */
        $regularUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        $groupsUserIsNotApartOf = $this->groupRepository->findGroupsUserIsNotApartOf($regularUser);

        $devicesNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);
        $deviceNotApartOf = $devicesNotApartOf[0];

        $sensorsNotApartOf = $this->sensorRepository->findBy(['deviceID' => $deviceNotApartOf]);
        $sensorNotApartOf = $sensorsNotApartOf[0];

        $baseReadingTypesNotApartOf = $this->baseSensorReadingTypeRepository->findBy(['sensor' => $sensorNotApartOf]);
        $baseReadingTypeNotApartOf = $baseReadingTypesNotApartOf[0];

        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_TWO, UserDataFixtures::REGULAR_PASSWORD);

        $operator = $this->operatorRepository->findAll()[0];
        $triggerType = $this->triggerTypeRepository->findAll()[0];

        $groupsUserIsApartOf = $regularUser->getAssociatedGroupIDs();

        $devicesApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);
        $deviceApartOf = $devicesApartOf[0];

        $sensorsApartOf = $this->sensorRepository->findBy(['deviceID' => $deviceApartOf]);
        $sensorApartOf = $sensorsApartOf[0];

        $baseReadingTypesApartOf = $this->baseSensorReadingTypeRepository->findBy(['sensor' => $sensorApartOf]);
        $baseReadingTypeApartOf = $baseReadingTypesApartOf[0];

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_TRIGGER_URL,
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $userToken,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(
                [
                    'operator' => $operator->getOperatorID(),
                    'triggerType' => $triggerType->getTriggerTypeID(),
                    'baseReadingTypeThatTriggers' => $baseReadingTypeNotApartOf->getBaseReadingTypeID(),
                    'baseReadingTypeThatIsTriggered' => $baseReadingTypeApartOf->getBaseReadingTypeID(),
                    'days' => ['monday'],
                    'valueThatTriggers' => 1.1,
                    'startTime' => 1000,
                    'endTime' => 2000,
                ],
                JSON_THROW_ON_ERROR
            )
        );

        $response = $this->client->getResponse();
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('errors', $responseData);

        $title = $responseData['title'];
        self::assertEquals(AddSensorTriggerController::NOT_AUTHORIZED_TO_BE_HERE, $title);
    }

    /**
     * @dataProvider operatorAndTriggerTypeProvider
     */
    public function test_admin_can_create_trigger_for_sensor_group_not_apart_of(
        string $operatorSymbol,
        string $triggerTypeName,
    ): void {
        /** @var User $adminUser */
        $adminUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]);
        $groupsUserIsNotApartOf = $this->groupRepository->findGroupsUserIsNotApartOf($adminUser);

        $devicesNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);
        $deviceNotApartOf = $devicesNotApartOf[0];

        $sensorsNotApartOf = $this->sensorRepository->findBy(['deviceID' => $deviceNotApartOf]);
        $sensorNotApartOf = $sensorsNotApartOf[0];

        $baseReadingTypesNotApartOf = $this->baseSensorReadingTypeRepository->findBy(['sensor' => $sensorNotApartOf]);
        $baseReadingTypeThatTriggers = $baseReadingTypesNotApartOf[0];

        $operator = $this->operatorRepository->findOneBy(['operatorSymbol' => $operatorSymbol]);
        $triggerType = $this->triggerTypeRepository->findOneBy(['triggerTypeName' => $triggerTypeName]);

        $groupsUserIsApartOf = $adminUser->getAssociatedGroupIDs();

        $devicesApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);
        $deviceApartOf = $devicesApartOf[0];

        /** @var GenericRelay $relaySensorType */
        $relaySensorType = $this->entityManager->getRepository(GenericRelay::class)->findAll()[0];
        $sensorsApartOf = $this->sensorRepository->findBy(['deviceID' => $deviceApartOf, 'sensorTypeID' => $relaySensorType->getSensorTypeID()]);
        $sensorApartOf = $sensorsApartOf[0];

        $baseReadingTypesApartOf = $this->baseSensorReadingTypeRepository->findBy(['sensor' => $sensorApartOf]);
        $baseReadingTypeThatIsTriggered = $baseReadingTypesApartOf[0];

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_TRIGGER_URL,
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(
                [
                    'operator' => $operator->getOperatorID(),
                    'triggerType' => $triggerType->getTriggerTypeID(),
                    'baseReadingTypeThatTriggers' => $baseReadingTypeThatTriggers->getBaseReadingTypeID(),
                    'baseReadingTypeThatIsTriggered' => $baseReadingTypeThatIsTriggered->getBaseReadingTypeID(),
                    'days' => ['monday'],
                    'valueThatTriggers' => 1.1,
                    'startTime' => 1000,
                    'endTime' => 2000,
                ],
                JSON_THROW_ON_ERROR
            )
        );

        $response = $this->client->getResponse();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayNotHasKey('errors', $responseData);

        $title = $responseData['title'];
        self::assertEquals(AddSensorTriggerController::REQUEST_SUCCESSFUL, $title);

        $payload = $responseData['payload'];
        $sensorTriggerID = $payload['sensorTriggerID'];

        $sensorTrigger = $this->sensorTriggerRepository->find($sensorTriggerID);
        self::assertNotNull($sensorTrigger);

        $operatorResponse = $payload['operator'];
        self::assertEquals($operator->getOperatorID(), $operatorResponse['operatorID']);
        self::assertEquals($operator->getOperatorName(), $operatorResponse['operatorName']);
        self::assertEquals($operator->getOperatorSymbol(), $operatorResponse['operatorSymbol']);
        self::assertEquals($operator->getOperatorDescription(), $operatorResponse['operatorDescription']);

        $triggerTypeResponse = $payload['triggerType'];
        self::assertEquals($triggerType->getTriggerTypeID(), $triggerTypeResponse['triggerTypeID']);
        self::assertEquals($triggerType->getTriggerTypeName(), $triggerTypeResponse['triggerTypeName']);
        self::assertEquals($triggerType->getTriggerTypeDescription(), $triggerTypeResponse['triggerTypeDescription']);

        $baseReadingTypeThatTriggersResponse = $payload['baseReadingTypeThatTriggers'];
        self::assertEquals($baseReadingTypeThatTriggersResponse['baseReadingTypeID'], $baseReadingTypeThatTriggers->getBaseReadingTypeID());

        $baseReadingTypeThatIsTriggeredResponse = $payload['baseReadingTypeThatIsTriggered'];
        self::assertEquals($baseReadingTypeThatIsTriggeredResponse['baseReadingTypeID'], $baseReadingTypeThatIsTriggered->getBaseReadingTypeID());
    }

    public function operatorAndTriggerTypeProvider(): Generator
    {
        yield [
            'operatorName' => Operator::OPERATOR_EQUAL,
            'triggerTypeName' => TriggerType::EMAIL_TRIGGER,
        ];

        yield [
            'operatorName' => Operator::OPERATOR_NOT_EQUAL,
            'triggerTypeName' => TriggerType::RELAY_UP_TRIGGER,
        ];

        yield [
            'operatorName' => Operator::OPERATOR_GREATER_THAN_OR_EQUAL,
            'triggerTypeName' => TriggerType::RELAY_DOWN_TRIGGER,
        ];

        yield [
            'operatorName' => Operator::OPERATOR_LESS_THAN_OR_EQUAL,
            'triggerTypeName' => TriggerType::RELAY_UP_TRIGGER,
        ];

        yield [
            'operatorName' => Operator::OPERATOR_GREATER_THAN,
            'triggerTypeName' => TriggerType::RELAY_DOWN_TRIGGER,
        ];

        yield [
            'operatorName' => Operator::OPERATOR_LESS_THAN,
            'triggerTypeName' => TriggerType::RELAY_UP_TRIGGER,
        ];

        yield [
            'operatorName' => Operator::OPERATOR_EQUAL,
            'triggerTypeName' => TriggerType::RELAY_DOWN_TRIGGER,
        ];

        yield [
            'operatorName' => Operator::OPERATOR_NOT_EQUAL,
            'triggerTypeName' => TriggerType::RELAY_UP_TRIGGER,
        ];
    }

    /**
     * @dataProvider operatorAndTriggerTypeProvider
     */
    public function test_user_can_create_trigger_sensor_apart_of(
        string $operatorSymbol,
        string $triggerTypeName,
    ): void {
        /** @var User $regularUser */
        $regularUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        $groupsUserIsNotApartOf = $this->groupRepository->findGroupsUserIsApartOf($regularUser);

        $devicesNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);
        $deviceNotApartOf = $devicesNotApartOf[0];

        $sensorsNotApartOf = $this->sensorRepository->findBy(['deviceID' => $deviceNotApartOf]);
        $sensorNotApartOf = $sensorsNotApartOf[0];

        $baseReadingTypesNotApartOf = $this->baseSensorReadingTypeRepository->findBy(['sensor' => $sensorNotApartOf]);
        $baseReadingTypeThatTriggers = $baseReadingTypesNotApartOf[0];

        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_TWO, UserDataFixtures::REGULAR_PASSWORD);

        $operator = $this->operatorRepository->findOneBy(['operatorSymbol' => $operatorSymbol]);
        $triggerType = $this->triggerTypeRepository->findOneBy(['triggerTypeName' => $triggerTypeName]);

        $groupsUserIsApartOf = $regularUser->getAssociatedGroupIDs();

        $devicesApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);
        $deviceApartOf = $devicesApartOf[0];

        /** @var GenericRelay $relaySensorType */
        $relaySensorType = $this->entityManager->getRepository(GenericRelay::class)->findAll()[0];
        $sensorsApartOf = $this->sensorRepository->findBy(['deviceID' => $deviceApartOf, 'sensorTypeID' => $relaySensorType->getSensorTypeID()]);
        $sensorApartOf = $sensorsApartOf[0];

        $baseReadingTypesApartOf = $this->baseSensorReadingTypeRepository->findBy(['sensor' => $sensorApartOf]);
        $baseReadingTypeThatIsTriggered = $baseReadingTypesApartOf[0];

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_TRIGGER_URL,
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $userToken,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(
                [
                    'operator' => $operator->getOperatorID(),
                    'triggerType' => $triggerType->getTriggerTypeID(),
                    'baseReadingTypeThatTriggers' => $baseReadingTypeThatTriggers->getBaseReadingTypeID(),
                    'baseReadingTypeThatIsTriggered' => $baseReadingTypeThatIsTriggered->getBaseReadingTypeID(),
                    'days' => ['monday'],
                    'valueThatTriggers' => 1.1,
                    'startTime' => 1000,
                    'endTime' => 2000,
                ],
                JSON_THROW_ON_ERROR
            )
        );

        $response = $this->client->getResponse();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayNotHasKey('errors', $responseData);

        $title = $responseData['title'];
        self::assertEquals(AddSensorTriggerController::REQUEST_SUCCESSFUL, $title);

        $payload = $responseData['payload'];
        $sensorTriggerID = $payload['sensorTriggerID'];

        $sensorTrigger = $this->sensorTriggerRepository->find($sensorTriggerID);
        self::assertNotNull($sensorTrigger);

        $operatorResponse = $payload['operator'];
        self::assertEquals($operator->getOperatorID(), $operatorResponse['operatorID']);
        self::assertEquals($operator->getOperatorName(), $operatorResponse['operatorName']);
        self::assertEquals($operator->getOperatorSymbol(), $operatorResponse['operatorSymbol']);
        self::assertEquals($operator->getOperatorDescription(), $operatorResponse['operatorDescription']);

        $triggerTypeResponse = $payload['triggerType'];
        self::assertEquals($triggerType->getTriggerTypeID(), $triggerTypeResponse['triggerTypeID']);
        self::assertEquals($triggerType->getTriggerTypeName(), $triggerTypeResponse['triggerTypeName']);
        self::assertEquals($triggerType->getTriggerTypeDescription(), $triggerTypeResponse['triggerTypeDescription']);

        $baseReadingTypeThatTriggersResponse = $payload['baseReadingTypeThatTriggers'];
        self::assertEquals($baseReadingTypeThatTriggersResponse['baseReadingTypeID'], $baseReadingTypeThatTriggers->getBaseReadingTypeID());

        $baseReadingTypeThatIsTriggeredResponse = $payload['baseReadingTypeThatIsTriggered'];
        self::assertEquals($baseReadingTypeThatIsTriggeredResponse['baseReadingTypeID'], $baseReadingTypeThatIsTriggered->getBaseReadingTypeID());
    }
}
