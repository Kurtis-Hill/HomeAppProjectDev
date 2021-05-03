<?php


namespace App\Tests\Controller\Sensors;


use App\Controller\Core\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Sensors\SensorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SensorController extends WebTestCase
{
    private const GET_SENSOR_TYPES = '/HomeApp/api/sensors/types';

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var KernelBrowser
     */
    private KernelBrowser $client;

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

//        $this->groupName = $this->entityManager->getRepository(GroupNames::class)->findGroupByName(UserDataFixtures::ADMIN_GROUP);
//        $this->room = $this->entityManager->getRepository(Room::class)->findRoomByGroupNameAndName($this->groupName, RoomFixtures::ADMIN_ROOM_NAME);
        try {
            $this->setUserToken();
        } catch (\JsonException $e) {
            error_log($e);
        }
    }

//    public function test_can_add_new_sensor_correct_details()
//    {
//
//    }

    public function test_return_all_sensor_types()
    {
        $this->client->request(
            'GET',
            self::GET_SENSOR_TYPES,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $totalSensorTypes = count(SensorType::SENSOR_TYPES);

        $amountOfSensorTypesInResponse = count(json_decode($this->client->getResponse()->getContent(), true));

        self::assertEquals($totalSensorTypes, $amountOfSensorTypesInResponse);
    }

    /**
     * @return mixed|string|KernelBrowser|null
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
}
