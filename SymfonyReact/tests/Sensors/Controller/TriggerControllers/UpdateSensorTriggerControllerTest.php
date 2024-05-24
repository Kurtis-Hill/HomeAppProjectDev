<?php

namespace App\Tests\Sensors\Controller\TriggerControllers;

use App\Common\Entity\Operator;
use App\Common\Entity\TriggerType;
use App\Common\Repository\OperatorRepository;
use App\Common\Repository\TriggerTypeRepository;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepository;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Controller\TriggerControllers\UpdateSensorTriggerController;
use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTrigger;
use App\Sensors\Repository\ReadingType\ORM\BaseSensorReadingTypeRepository;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\Repository\SensorTriggerRepository;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Group;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupRepository;
use App\User\Repository\ORM\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateSensorTriggerControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const UPDATE_TRIGGER_URL = '/HomeApp/api/user/sensor-trigger/%d/update';

    private KernelBrowser $client;

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private SensorRepositoryInterface $sensorRepository;

    private SensorTriggerRepository $sensorTriggerRepository;

    private OperatorRepository $operatorRepository;

    private BaseSensorReadingTypeRepository $baseSensorReadingTypeRepository;

    private TriggerTypeRepository $triggerTypeRepository;

    private GroupRepository $groupRepository;

    private UserRepository $userRepository;

    private DeviceRepository $deviceRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userToken = $this->setUserToken($this->client);

        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $this->sensorTriggerRepository = $this->entityManager->getRepository(SensorTrigger::class);
        $this->operatorRepository = $this->entityManager->getRepository(Operator::class);
        $this->baseSensorReadingTypeRepository = $this->entityManager->getRepository(BaseSensorReadingType::class);
        $this->triggerTypeRepository = $this->entityManager->getRepository(TriggerType::class);
        $this->groupRepository = $this->entityManager->getRepository(Group::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    /**
     * @dataProvider updatingWithWrongDataTypesDataProvider
     */
    public function test_updating_with_wrong_data_types(
        mixed $operator,
        mixed $triggerType,
        mixed $days,
        mixed $valueThatTriggers,
        mixed $baseReadingTypeThatTriggers,
        mixed $baseReadingTypeThatIsTriggered,
        mixed $startTime,
        mixed $endTime,
        mixed $override,
        mixed $errorMessage
    ): void {
        $sensorTrigger = $this->sensorTriggerRepository->findAll()[0];
        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_TRIGGER_URL, $sensorTrigger->getSensorTriggerID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            json_encode([
                'operator' => $operator,
                'triggerType' => $triggerType,
                'days' => $days,
                'valueThatTriggers' => $valueThatTriggers,
                'baseReadingTypeThatTriggers' => $baseReadingTypeThatTriggers,
                'baseReadingTypeThatIsTriggered' => $baseReadingTypeThatIsTriggered,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'override' => $override,
            ]),
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $title = $responseData['title'] ?? null;
        $errors = $responseData['errors'] ?? null;

        self::assertEquals(UpdateSensorTriggerController::BAD_REQUEST_NO_DATA_RETURNED, $title);
        self::assertEquals($errorMessage, $errors);
    }

    public function updatingWithWrongDataTypesDataProvider(): Generator
    {
        yield [
            'operator' => 'string',
            'triggerType' => 1,
            'days' => ['monday'],
            'valueThatTriggers' => false,
            'baseReadingTypeThatTriggers' => 2,
            'baseReadingTypeThatIsTriggered' => 2,
            'startTime' => '1010',
            'endTime' => '9042',
            'override' => false,
            'errorMessage' => [
                'operator must be an int|null you have provided "string"',
            ],
        ];

        yield [
            'operator' => 1,
            'triggerType' => 'string',
            'days' => ['monday'],
            'valueThatTriggers' => false,
            'baseReadingTypeThatTriggers' => 2,
            'baseReadingTypeThatIsTriggered' => 2,
            'startTime' => '1010',
            'endTime' => '9042',
            'override' => false,
            'errorMessage' => [
                'trigger type must be an int|null you have provided "string"',
            ],
        ];

        yield [
            'operator' => 1,
            'triggerType' => 1,
            'days' => 'string',
            'valueThatTriggers' => false,
            'baseReadingTypeThatTriggers' => 2,
            'baseReadingTypeThatIsTriggered' => 2,
            'startTime' => '1010',
            'endTime' => '9042',
            'override' => false,
            'errorMessage' => [
                'days must be an array|null you have provided "string"',
                'This value should be of type iterable.',
            ],
        ];

        yield [
            'operator' => 1,
            'triggerType' => 1,
            'days' => ['monday'],
            'valueThatTriggers' => 'string',
            'baseReadingTypeThatTriggers' => 2,
            'baseReadingTypeThatIsTriggered' => 2,
            'startTime' => '1010',
            'endTime' => '9042',
            'override' => false,
            'errorMessage' => [
                'value that triggers must be an bool|float|int|null you have provided "string"',
            ],
        ];

        yield [
            'operator' => 1,
            'triggerType' => 1,
            'days' => ['monday'],
            'valueThatTriggers' => false,
            'baseReadingTypeThatTriggers' => 'string',
            'baseReadingTypeThatIsTriggered' => 2,
            'startTime' => '1010',
            'endTime' => '9042',
            'override' => false,
            'errorMessage' => [
                'base reading type that triggers must be an int|null you have provided "string"',
            ],
        ];

        yield [
            'operator' => 1,
            'triggerType' => 1,
            'days' => ['monday'],
            'valueThatTriggers' => false,
            'baseReadingTypeThatTriggers' => 2,
            'baseReadingTypeThatIsTriggered' => 'string',
            'startTime' => '1010',
            'endTime' => '9042',
            'override' => false,
            'errorMessage' => [
                'base reading type that is triggered must be an int|null you have provided "string"',
            ],
        ];

        yield [
            'operator' => 1,
            'triggerType' => 1,
            'days' => ['monday'],
            'valueThatTriggers' => false,
            'baseReadingTypeThatTriggers' => 2,
            'baseReadingTypeThatIsTriggered' => 2,
            'startTime' => 1010,
            'endTime' => '9042',
            'override' => false,
            'errorMessage' => [
                'start time must be an string|null you have provided 1010',
            ],
        ];

        yield [
            'operator' => 1,
            'triggerType' => 1,
            'days' => ['monday'],
            'valueThatTriggers' => false,
            'baseReadingTypeThatTriggers' => 2,
            'baseReadingTypeThatIsTriggered' => 2,
            'startTime' => '1010',
            'endTime' => 9042,
            'override' => false,
            'errorMessage' => [
                'end time must be an string|null you have provided 9042',
            ],
        ];

        yield [
            'operator' => 1,
            'triggerType' => 1,
            'days' => ['monday'],
            'valueThatTriggers' => false,
            'baseReadingTypeThatTriggers' => 2,
            'baseReadingTypeThatIsTriggered' => 2,
            'startTime' => '1010',
            'endTime' => '9042',
            'override' => 'string',
            'errorMessage' => [
                'override must be an bool|null you have provided "string"',
            ],
        ];
    }

    public function test_updating_with_sensor_ids_dont_exist(): void
    {
        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_TRIGGER_URL, 21342314),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            json_encode([
                'days' => ['sunday']
            ]),
        );

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function test_user_that_cannot_update_gets_access_denied(): void
    {
        $regularUser = $this->findRegularUserOne($this->entityManager);

        $groupsUserIsNotApartOf = $this->groupRepository->findGroupsUserIsNotApartOf($regularUser);

        $devicesUserIsNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);

        $sensorsUserIsNotApartOf = $this->sensorRepository->findBy(['deviceID' => $devicesUserIsNotApartOf]);

        $baseReadingTypesBySensor = $this->baseSensorReadingTypeRepository->findBy(['sensor' => $sensorsUserIsNotApartOf]);

        $sensorTriggerUserIsNotApartOf = $this->sensorTriggerRepository->findBy(['baseReadingTypeThatTriggers' => $baseReadingTypesBySensor]);

        $sensorTriggerUserIsNotApartOf = $sensorTriggerUserIsNotApartOf[array_rand($sensorTriggerUserIsNotApartOf)];

        $randomOperator = $this->operatorRepository->findAll();
        /** @var Operator $randomOperator */
        $randomOperator = $randomOperator[array_rand($randomOperator)];
        $randomTriggerType = $this->triggerTypeRepository->findAll();
        /** @var TriggerType $randomTriggerType */
        $randomTriggerType = $randomTriggerType[array_rand($randomTriggerType)];

        $userToken = $this->setUserToken($this->client, $regularUser->getEmail(), UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_TRIGGER_URL, $sensorTriggerUserIsNotApartOf->getSensorTriggerID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
            json_encode([
                'operator' => $randomOperator->getOperatorID(),
                'triggerType' => $randomTriggerType->getTriggerTypeID(),
                'days' => ['sunday']
            ]),
        );

        self::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function test_user_that_can_update_gets_success(): void
    {
        $regularUser = $this->findRegularUserTwo($this->entityManager);

        $groupsUserIsIn = $regularUser->getAssociatedGroupIDs();

        $devicesUserIsApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsIn]);

        $sensorsUserIsApartOf = $this->sensorRepository->findBy(['deviceID' => $devicesUserIsApartOf]);

        $baseReadingTypesBySensor = $this->baseSensorReadingTypeRepository->findBy(['sensor' => $sensorsUserIsApartOf]);

        $sensorTriggersUserIsApartOf = $this->sensorTriggerRepository->findBy(['baseReadingTypeThatTriggers' => $baseReadingTypesBySensor]);

        /** @var SensorTrigger $itme */
        foreach ($sensorTriggersUserIsApartOf as $item) {
            $baseReadingTypeThatIsTriggeredGroupID = $item->getBaseReadingTypeToTriggers()->getSensor()->getDevice()->getGroupObject()->getGroupID();
            $baseReadingTypeThatTriggersGroupID = $item->getBaseReadingTypeThatTriggers()->getSensor()->getDevice()->getGroupObject()->getGroupID();
            if (in_array($baseReadingTypeThatIsTriggeredGroupID, $groupsUserIsIn) && in_array($baseReadingTypeThatTriggersGroupID, $groupsUserIsIn)) {
                $sensorTriggerUserIsApartOf = $item;
                break;
            }
        }

        $randomOperator = $this->operatorRepository->findAll();
        /** @var Operator $randomOperator */
        $randomOperator = $randomOperator[array_rand($randomOperator)];
        $randomTriggerType = $this->triggerTypeRepository->findAll();
        /** @var TriggerType $randomTriggerType */
        $randomTriggerType = $randomTriggerType[array_rand($randomTriggerType)];

        $userToken = $this->setUserToken($this->client, $regularUser->getEmail(), UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_TRIGGER_URL, $sensorTriggerUserIsApartOf->getSensorTriggerID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
            json_encode([
                'operator' => $randomOperator->getOperatorID(),
                'triggerType' => $randomTriggerType->getTriggerTypeID(),
                'days' => ['sunday']
            ]),
        );

        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        /** @var SensorTrigger $trigger */
        $trigger = $this->sensorTriggerRepository->find($sensorTriggerUserIsApartOf->getSensorTriggerID());
        self::assertTrue($trigger->getSunday());

        self::assertEquals($randomOperator->getOperatorID(), $trigger->getOperator()->getOperatorID());
        self::assertEquals($randomOperator->getOperatorName(), $trigger->getOperator()->getOperatorName());
        self::assertEquals($randomOperator->getOperatorSymbol(), $trigger->getOperator()->getOperatorSymbol());

        self::assertEquals($randomTriggerType->getTriggerTypeID(), $trigger->getTriggerType()->getTriggerTypeID());
        self::assertEquals($randomTriggerType->getTriggerTypeName(), $trigger->getTriggerType()->getTriggerTypeName());
        self::assertEquals($randomTriggerType->getTriggerTypeDescription(), $trigger->getTriggerType()->getTriggerTypeDescription());
    }

    public function test_admin_user_can_update_all_triggers(): void
    {
        $randomOperator = $this->operatorRepository->findAll();
        /** @var Operator $randomOperator */
        $randomOperator = $randomOperator[array_rand($randomOperator)];
        $randomTriggerType = $this->triggerTypeRepository->findAll();
        /** @var TriggerType $randomTriggerType */
        $randomTriggerType = $randomTriggerType[array_rand($randomTriggerType)];
        $baseReadingTypeThatTriggers = $this->baseSensorReadingTypeRepository->findAll();
        /** @var BaseSensorReadingType $baseReadingTypeThatTriggers */
        $baseReadingTypeThatTriggers = $baseReadingTypeThatTriggers[array_rand($this->baseSensorReadingTypeRepository->findAll())];
        $baseReadingTypeThatIsTriggered = $this->baseSensorReadingTypeRepository->findAll();
        /** @var BaseSensorReadingType $baseReadingTypeThatIsTriggered */
        $baseReadingTypeThatIsTriggered = $baseReadingTypeThatIsTriggered[array_rand($baseReadingTypeThatIsTriggered)];

        $days = [
            'monday',
            'tuesday',
            'wednesday',
            'friday',
            'saturday',
            'sunday',
        ];
        $valueThatTriggers = 100;
        $startTime = "1010";
        $endTime = "1212";
        $override = true;

        $triggerUpdate = [
            'operator' => $randomOperator->getOperatorID(),
            'triggerType' => $randomTriggerType->getTriggerTypeID(),
            'baseReadingTypeThatTriggers' => $baseReadingTypeThatTriggers->getBaseReadingTypeID(),
            'baseReadingTypeThatIsTriggered' => $baseReadingTypeThatIsTriggered->getBaseReadingTypeID(),
            'days' => $days,
            'valueThatTriggers' => $valueThatTriggers,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'override' => $override,
        ];

        $triggerDataToSend = json_encode($triggerUpdate);
        $allSensorTriggers = $this->sensorTriggerRepository->findAll();

        /** @var SensorTrigger $trigger */
        foreach ($allSensorTriggers as $trigger) {
            $this->client->request(
                Request::METHOD_PUT,
                sprintf(self::UPDATE_TRIGGER_URL, $trigger->getSensorTriggerID()),
                [],
                [],
                ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
                $triggerDataToSend,
            );

            self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
            /** @var SensorTrigger $sensorTriggerAfterUpdate */
            $sensorTriggerAfterUpdate = $this->sensorTriggerRepository->find($trigger->getSensorTriggerID());

            self::assertEquals($randomOperator->getOperatorID(), $sensorTriggerAfterUpdate->getOperator()->getOperatorID());
            self::assertEquals($randomOperator->getOperatorName(), $sensorTriggerAfterUpdate->getOperator()->getOperatorName());
            self::assertEquals($randomOperator->getOperatorSymbol(), $sensorTriggerAfterUpdate->getOperator()->getOperatorSymbol());
            self::assertEquals($randomOperator->getOperatorDescription(), $sensorTriggerAfterUpdate->getOperator()->getOperatorDescription());

            self::assertEquals($randomTriggerType->getTriggerTypeID(), $sensorTriggerAfterUpdate->getTriggerType()->getTriggerTypeID());
            self::assertEquals($randomTriggerType->getTriggerTypeName(), $sensorTriggerAfterUpdate->getTriggerType()->getTriggerTypeName());
            self::assertEquals($randomTriggerType->getTriggerTypeDescription(), $sensorTriggerAfterUpdate->getTriggerType()->getTriggerTypeDescription());

            self::assertEquals($valueThatTriggers, $sensorTriggerAfterUpdate->getValueThatTriggers());

            self::assertEquals($startTime, $sensorTriggerAfterUpdate->getStartTime());
            self::assertEquals($endTime, $sensorTriggerAfterUpdate->getEndTime());

            self::assertEquals($baseReadingTypeThatTriggers->getBaseReadingTypeID(), $sensorTriggerAfterUpdate->getBaseReadingTypeThatTriggers()->getBaseReadingTypeID());
            self::assertEquals($baseReadingTypeThatIsTriggered->getBaseReadingTypeID(), $sensorTriggerAfterUpdate->getBaseReadingTypeToTriggers()->getBaseReadingTypeID());

        }

    }

    public function test_success_response_contains_all_data(): void
    {
        $randomOperator = $this->operatorRepository->findAll();
        /** @var Operator $randomOperator */
        $randomOperator = $randomOperator[array_rand($randomOperator)];
        $randomTriggerType = $this->triggerTypeRepository->findAll();
        /** @var TriggerType $randomTriggerType */
        $randomTriggerType = $randomTriggerType[array_rand($randomTriggerType)];
        $baseReadingTypeThatTriggers = $this->baseSensorReadingTypeRepository->findAll();
        /** @var BaseSensorReadingType $baseReadingTypeThatTriggers */
        $baseReadingTypeThatTriggers = $baseReadingTypeThatTriggers[array_rand($this->baseSensorReadingTypeRepository->findAll())];
        $baseReadingTypeThatIsTriggered = $this->baseSensorReadingTypeRepository->findAll();
        /** @var BaseSensorReadingType $baseReadingTypeThatIsTriggered */
        $baseReadingTypeThatIsTriggered = $baseReadingTypeThatIsTriggered[array_rand($baseReadingTypeThatIsTriggered)];

        $days = [
            'monday',
            'tuesday',
            'wednesday',
            'friday',
            'saturday',
            'sunday',
        ];
        $valueThatTriggers = 100;
        $startTime = "1010";
        $endTime = "1212";
        $override = true;

        $triggerUpdate = [
            'operator' => $randomOperator->getOperatorID(),
            'triggerType' => $randomTriggerType->getTriggerTypeID(),
            'baseReadingTypeThatTriggers' => $baseReadingTypeThatTriggers->getBaseReadingTypeID(),
            'baseReadingTypeThatIsTriggered' => $baseReadingTypeThatIsTriggered->getBaseReadingTypeID(),
            'days' => $days,
            'valueThatTriggers' => $valueThatTriggers,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'override' => $override,
        ];

        $triggerDataToSend = json_encode($triggerUpdate);
        $allSensorTriggers = $this->sensorTriggerRepository->findAll();
        $trigger = $allSensorTriggers[array_rand($allSensorTriggers)];

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_TRIGGER_URL, $trigger->getSensorTriggerID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $triggerDataToSend,
        );
        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $title = $responseData['title'] ?? null;
        $payload = $responseData['payload'] ?? null;

        self::assertEquals(UpdateSensorTriggerController::REQUEST_SUCCESSFUL, $title);

        $operatorPayload = $payload['operator'] ?? null;
        self::assertEquals($randomOperator->getOperatorID(), $operatorPayload['operatorID']);
        self::assertEquals($randomOperator->getOperatorName(), $operatorPayload['operatorName']);
        self::assertEquals($randomOperator->getOperatorSymbol(), $operatorPayload['operatorSymbol']);
        self::assertEquals($randomOperator->getOperatorDescription(), $operatorPayload['operatorDescription']);

        $triggerTypePayload = $payload['triggerType'] ?? null;
        self::assertEquals($randomTriggerType->getTriggerTypeID(), $triggerTypePayload['triggerTypeID']);
        self::assertEquals($randomTriggerType->getTriggerTypeName(), $triggerTypePayload['triggerTypeName']);
        self::assertEquals($randomTriggerType->getTriggerTypeDescription(), $triggerTypePayload['triggerTypeDescription']);

        $valueThatTriggers = $payload['valueThatTriggers'] ?? null;
        self::assertEquals($valueThatTriggers, $valueThatTriggers);

        $startTimePayload = $payload['startTime'] ?? null;
        self::assertEquals($startTime, $startTimePayload);

        $endTimePayload = $payload['endTime'] ?? null;
        self::assertEquals($endTime, $endTimePayload);

        $baseReadingTypeThatTriggersPayload = $payload['baseReadingTypeThatTriggers'] ?? null;
        self::assertEquals($baseReadingTypeThatTriggers->getBaseReadingTypeID(), $baseReadingTypeThatTriggersPayload['baseReadingTypeID']);

        $baseReadingTypeThatIsTriggeredPayload = $payload['baseReadingTypeThatIsTriggered'] ?? null;
        self::assertEquals($baseReadingTypeThatIsTriggered->getBaseReadingTypeID(), $baseReadingTypeThatIsTriggeredPayload['baseReadingTypeID']);

        self::assertEquals($override, $payload['override']);

        foreach ($days as $day) {
            self::assertEquals($day, $payload[$day]);
        }
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $triggerToUser = $this->sensorTriggerRepository->findAll()[0];
        $this->client->request(
            $httpVerb,
            sprintf(self::UPDATE_TRIGGER_URL, $triggerToUser->getSensorTriggerID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_GET],
            [Request::METHOD_POST],
            [Request::METHOD_DELETE],
        ];
    }
}
