<?php


namespace App\Tests\ESPSensorTests;


use App\API\HTTPStatusCodes;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\DeviceFixtures;
use App\Entity\Core\GroupNames;
use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\Room;
use App\Entity\Core\User;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorTypes\Dht;
use App\Repository\Core\DevicesRepository;
use App\Repository\Core\UserRepository;
use App\Services\ESPDeviceSensor\Devices\DeviceServiceUser;
use App\Services\ESPDeviceSensor\SensorData\SensorUserDataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SensorUserDataServiceTest extends WebTestCase
{
    private const ADD_NEW_SENSOR_PATH = '/HomeApp/api/sensors/add-new-sensor';

    private const API_USER_LOGIN = '/HomeApp/api/login_check';

    private const TEST_DEVICE = 101;

    private const UNIQUE_SENSOR_NAME = 'ApiSensorTest';

    /**
     * @var string|null
     */
    private ?string $adminUserToken = null;

    /**
     * @var string|null
     */
    private ?string $adminUserRefreshToken = null;


    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;

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

    public function test_user_login()
    {
        self::assertNotNull($this->adminUserToken);
    }

//    //@TODO need to add sensor types first finish fixtures
//    public function test_creating_new_valid_dht_sensor()
//    {
//        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
//
//        $device = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $user->getGroupNameID()])[0];
//
//        $formData = [
//            'sensor-name' => self::UNIQUE_SENSOR_NAME,
//            'device-id' => $device->getDeviceNameID(),
//            'sensor-type' => Dht::SENSOR_TYPE_ID
//        ];
//
//        $this->client->request(
//            'POST',
//            self::ADD_NEW_SENSOR_PATH,
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->adminUserToken],
//        );
//
//        dd($this->client->getResponse()->getContent(), $device, $formData);
//        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
//    }


//    public function test_creating_new_invalid_sensor_by_duplicate_sensor_name()
//    {
////        $duplicateSensor = self::$container->get(DevicesRepository::class)->findDeviceByGroupAndDeviceName();
//        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
//
//        $groupNameMappingRepository = $this->entityManager->getRepository(GroupnNameMapping::class);
//
//        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
//        $user->setUserGroupMappingEntities($groupNameMappingEntities);
//
//        $duplicateSensor = $this->entityManager->getRepository(Sensors::class)->findAllSensorsByAssociatedGroups($user);
//
//        $formData = [
//            'sensor-name' => 'IAlreadyExists',
//            'device-id' => self::TEST_DEVICE,
//            'sensor-type' => Dht::SENSOR_TYPE_ID
//        ];
//
//
//        $this->client->request(
//            'POST',
//            '/HomeApp/api/sensors/add-new-sensor',
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->adminUserToken]
//        );
//
//        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
//    }


    public function test_creating_new_invalid_sensor_by_wrong_sensor_type()
    {
        $formData = [
            'sensor-name' => self::UNIQUE_SENSOR_NAME,
            'device-id' => self::TEST_DEVICE,
            'sensor-type' => 1000
        ];

        $this->client->request(
            'POST',
            '/HomeApp/api/sensors/add-new-sensor',
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->adminUserToken],
            'sensor-name=apitesting&device-id='.self::TEST_DEVICE.'&sensor-type='.Dht::SENSOR_TYPE_ID.''
        );

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
    }


    public function create_new_dallas_sensor_by_request_test()
    {

    }
    public function create_new_soil_sensor_by_request_test()
    {

    }
    public function create_new_bmp_sensor_by_request_test()
    {

    }

    public function update_sensor_outofbounds_reading()
    {

    }

    /**
     * @return mixed|string|KernelBrowser|null
     * @throws \JsonException
     */
    private function setUserToken()
    {
        if ($this->adminUserToken === null) {
            $this->client->request(
                'POST',
                self::API_USER_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"'.UserDataFixtures::ADMIN_USER.'","password":"'.UserDataFixtures::ADMIN_PASSWORD.'"}'
            );

            $requestResponse = $this->client->getResponse();
            $requestData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $this->adminUserToken = $requestData['token'];
            $this->adminUserRefreshToken = $requestData['refreshToken'];
        }
    }
}
