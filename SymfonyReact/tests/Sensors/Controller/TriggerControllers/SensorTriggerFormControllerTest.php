<?php

namespace App\Tests\Sensors\Controller\TriggerControllers;

use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepository;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\Sensors\ORM\SensorRepository;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Group;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SensorTriggerFormControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_SENSOR_TRIGGER_FORM_URL = '/HomeApp/api/user/sensor-trigger/form/get';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private ?string $userToken = null;

    private GroupRepository $groupRepository;

    private SensorRepository $sensorRepository;

    private DeviceRepository $deviceRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->groupRepository = $this->entityManager->getRepository(Group::class);
        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);

        try {
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

    public function test_sensors_user_should_not_see_dont_appear(): void
    {
        /** @var User $regularUser */
        $regularUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $userToken = $this->setUserToken($this->client, $regularUser->getEmail(), UserDataFixtures::REGULAR_PASSWORD);

        $groupsUserIsNotApartOf = $this->groupRepository->findGroupsUserIsNotApartOf($regularUser);

        $devicesUserIsNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);
        $sensorsUserIsNotApartOf = $this->sensorRepository->findBy(['deviceID' => $devicesUserIsNotApartOf]);

        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_TRIGGER_FORM_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
        );

//        dd($this->client->getResponse()->getContent());
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        
//        dd($responseData);
    }

    public function test_relay_sensors_user_should_not_see_dont_appear(): void
    {

    }

    public function test_admin_full_response_should_see_all(): void
    {

    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::GET_SENSOR_TRIGGER_FORM_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_POST],
            [Request::METHOD_PUT],
            [Request::METHOD_PATCH],
            [Request::METHOD_DELETE],
        ];
    }
}
