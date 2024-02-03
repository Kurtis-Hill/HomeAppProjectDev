<?php

namespace App\Tests\Sensors\Controller\TriggerControllers;

use App\Devices\Entity\Devices;
use App\ORM\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Sensors\Entity\Sensor;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use JsonException;
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

//    private ?Devices $device;

//    private DeviceRepository $deviceRepository;

//    private SensorRepositoryInterface $sensorRepository;

    private ?string $userToken = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

//        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
//        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);

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

    }

    public function test_user_cannot_add_trigger_from_sensor_not_apart_of(): void
    {

    }

    public function test_admin_can_create_trigger_for_any_sensor(): void
    {

    }

    public function test_user_can_create_trigger_sensor_apart_of(): void
    {

    }



}
