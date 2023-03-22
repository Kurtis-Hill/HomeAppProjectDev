<?php

namespace App\Tests\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepository;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Controller\SensorControllers\DeleteSensorController;
use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\Sensors\ORM\SensorRepository;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupNameRepository;
use App\User\Repository\ORM\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteSensorControllerTest extends WebTestCase
{
    private const DELETE_SENSOR_URL = CommonURL::USER_HOMEAPP_API_URL . 'sensor/%d/delete';

    use TestLoginTrait;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private ?string $userToken = null;

    private SensorRepository $sensorRepository;

    private UserRepository $userRepository;

    private GroupNameRepository $groupNameRepository;

    private DeviceRepository $deviceRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        try {
            $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
            $this->userRepository = $this->entityManager->getRepository(User::class);
            $this->groupNameRepository = $this->entityManager->getRepository(GroupNames::class);
            $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
            $this->userToken = $this->setUserToken($this->client);
        } catch (JsonException $e) {
            error_log($e);
        }
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_admin_user_can_delete_any_sensor(): void
    {
        /** @var Sensor[] $allSensors */
        $allSensors = $this->sensorRepository->findAll();

        foreach ($allSensors as $sensor) {
            $this->client->request(
                Request::METHOD_DELETE,
                sprintf(self::DELETE_SENSOR_URL, $sensor->getSensorID()),
                [],
                [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            );

            self::assertResponseStatusCodeSame(Response::HTTP_OK);

            $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $payload = $responseData['payload'];
            $title = $responseData['title'];

            self::assertEquals($sensor->getSensorID(), $payload['sensorNameID']);
            self::assertEquals($sensor->getSensorName(), $payload['sensorName']);
            self::assertEquals($sensor->getSensorTypeObject()->getSensorType(), $payload['sensorType']);
            self::assertEquals($sensor->getDevice()->getDeviceName(), $payload['deviceName']);
            self::assertEquals($sensor->getCreatedBy()->getEmail(), $payload['createdBy']);

            self::assertEquals(DeleteSensorController::DELETE_SENSOR_SUCCESS_MESSAGE, $title);
        }
    }

    public function test_regular_user_can_delete_sensors_part_of_same_device_group_name(): void
    {
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_ONE, UserDataFixtures::REGULAR_PASSWORD);

        $groupsUserIsPartOf = $this->groupNameRepository->findGroupsUserIsApartOf($user, $user->getAssociatedGroupNameIds());

        $devicesInGroupsUserIsPartOf = $this->deviceRepository->findBy(['groupNameID' => $groupsUserIsPartOf]);

        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findBy([
            'deviceID' => $devicesInGroupsUserIsPartOf,
        ]);

        foreach ($sensors as $sensor) {
            $this->client->request(
                Request::METHOD_DELETE,
                sprintf(self::DELETE_SENSOR_URL, $sensor->getSensorID()),
                [],
                [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
            );

            self::assertResponseStatusCodeSame(Response::HTTP_OK);

            $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $payload = $responseData['payload'];
            $title = $responseData['title'];

            self::assertEquals($sensor->getSensorID(), $payload['sensorNameID']);
            self::assertEquals($sensor->getSensorName(), $payload['sensorName']);
            self::assertEquals($sensor->getSensorTypeObject()->getSensorType(), $payload['sensorType']);
            self::assertEquals($sensor->getDevice()->getDeviceName(), $payload['deviceName']);
            self::assertEquals($sensor->getCreatedBy()->getEmail(), $payload['createdBy']);

            self::assertEquals(DeleteSensorController::DELETE_SENSOR_SUCCESS_MESSAGE, $title);
        }
    }

    public function test_regular_user_cannot_delete_sensors_part_of_different_device_group_name(): void
    {
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_ONE, UserDataFixtures::REGULAR_PASSWORD);

        $groupsUserIsNotPartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($user);

        $devicesInGroupsUserIsNotPartOf = $this->deviceRepository->findBy(['groupNameID' => $groupsUserIsNotPartOf]);

        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findBy([
            'deviceID' => $devicesInGroupsUserIsNotPartOf,
        ]);

        foreach ($sensors as $sensor) {
            $this->client->request(
                Request::METHOD_DELETE,
                sprintf(self::DELETE_SENSOR_URL, $sensor->getSensorID()),
                [],
                [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
            );

            self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

            $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $title = $responseData['title'];
            $errors = $responseData['errors'];

            self::assertEquals(DeleteSensorController::NOT_AUTHORIZED_TO_BE_HERE, $title);
            self::assertEquals(APIErrorMessages::ACCESS_DENIED, $errors[0]);
        }
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            sprintf(self::DELETE_SENSOR_URL, 1),
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
            [Request::METHOD_PUT],
            [Request::METHOD_PATCH],
        ];
    }
}
