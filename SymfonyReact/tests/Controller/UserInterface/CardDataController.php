<?php


namespace App\Tests\Controller\UserInterface;


use App\API\HTTPStatusCodes;
use App\Controller\Core\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Entity\Card\CardView;
use App\Entity\Core\GroupNames;
use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\User;
use App\Entity\Devices\Devices;
use App\HomeAppSensorCore\ESPDeviceSensor\AbstractHomeAppUserSensorServiceCore;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CardDataController extends WebTestCase
{
    private const API_CARD_DATA_RETURN_CARD_DTO_ROUTE = '/HomeApp/api/card-data/cards';

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

    /**
     * @var string|null
     */
    private ?string $userRefreshToken = null;

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
            $this->userRefreshToken = $requestData['refreshToken'];
        }
    }

    //return card data dto method

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

        self::assertCount($countDeviceCards, $responseData);
        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
