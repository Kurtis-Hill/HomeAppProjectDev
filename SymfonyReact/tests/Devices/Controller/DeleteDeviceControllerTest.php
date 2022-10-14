<?php

namespace App\Tests\Devices\Controller;

use App\Doctrine\DataFixtures\Core\UserDataFixtures;
use App\Authentication\Controller\SecurityController;
use App\Authentication\Entity\GroupNameMapping;
use App\Devices\Entity\Devices;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteDeviceControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const DELETE_DEVICE_URL = '/HomeApp/api/user/user-devices/delete-device/%d';

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userToken = $this->setUserToken($this->client);
    }

    public function testRegularUserCannotDeleteAdminDevice(): void
    {
        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER, UserDataFixtures::REGULAR_PASSWORD);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER]);
        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);

        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
        $user->setUserGroupMappingEntities($groupNameMappingEntities);
        $groupUserIsNotApartOf = $groupNameMappingRepository->findGroupsUserIsNotApartOf($user->getGroupNameIds())[0];

        $device = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $groupUserIsNotApartOf->getGroupNameID()])[0];

        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::DELETE_DEVICE_URL, $device->getDeviceNameID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$userToken],
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('You Are Not Authorised To Be Here', $responseData['title']);
        self::assertEquals('You have been denied permission to perform this action', $responseData['errors'][0]);
        self::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminUserCanDeleteDeviceNotApartOf(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::SECOND_ADMIN_USER_ISOLATED]);

        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);

        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
        $user->setUserGroupMappingEntities($groupNameMappingEntities);
        $groupUserIsNotApartOf = $groupNameMappingRepository->findGroupsUserIsNotApartOf($user->getGroupNameIds())[0];

        $device = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $groupUserIsNotApartOf->getGroupNameID()])[0];

        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::DELETE_DEVICE_URL, $device->getDeviceNameID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        $deletedDevice = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceNameID' => $device->getDeviceNameID()]);

        self::assertNull($deletedDevice);
        self::assertEquals('Request Successful', $responseData['title']);
        self::assertIsArray($responseData['payload']);
        self::assertEquals($device->getDeviceName(), $responseData['payload']['deviceName']);
        self::assertEquals($device->getGroupNameObject()->getGroupNameID(), $responseData['payload']['groupNameID']);
        self::assertEquals($device->getRoomObject()->getRoomID(), $responseData['payload']['roomID']);
        self::assertEquals($device->getCreatedBy()->getUserIdentifier(), $responseData['payload']['createdBy']);
        self::assertNull($responseData['payload']['secret']);
        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testDeletingDeviceThatDoesntExist(): void
    {
        $deviceRepository = $this->entityManager->getRepository(Devices::class);
        while (true) {
            $nonExistentDeviceID = random_int(1, 100000);

            $device = $deviceRepository->findOneBy(['deviceNameID' => $nonExistentDeviceID]);
            if (!$device instanceof Devices) {
                break;
            }
        }
        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::DELETE_DEVICE_URL, $nonExistentDeviceID),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider deletingDeviceDataProvider
     */
    public function testDeletingDevice(string $username): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $username]);

        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);

        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
        $user->setUserGroupMappingEntities($groupNameMappingEntities);

        $groupUserIsApartOf = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user)[0];

        $device = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $groupUserIsApartOf->getGroupNameID()])[0];

        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::DELETE_DEVICE_URL, $device->getDeviceNameID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );
        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $deletedDevice = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceNameID' => $device->getDeviceNameID()]);

        self::assertNull($deletedDevice);
        self::assertEquals('Request Successful', $responseData['title']);
        self::assertIsArray($responseData['payload']);
        self::assertEquals($device->getDeviceName(), $responseData['payload']['deviceName']);
        self::assertEquals($device->getGroupNameObject()->getGroupNameID(), $responseData['payload']['groupNameID']);
        self::assertEquals($device->getRoomObject()->getRoomID(), $responseData['payload']['roomID']);
        self::assertEquals($device->getCreatedBy()->getUserIdentifier(), $responseData['payload']['createdBy']);
        self::assertNull($responseData['payload']['secret']);
        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function deletingDeviceDataProvider(): Generator
    {
        yield [
            'username' => UserDataFixtures::ADMIN_USER
        ];

        yield [
            'username' => UserDataFixtures::REGULAR_USER
        ];
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }
}
