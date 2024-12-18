<?php

namespace App\Tests\Controller\Device;

use App\Controller\Device\DeleteDeviceController;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Device\Devices;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Repository\User\ORM\GroupRepositoryInterface;
use App\Repository\User\ORM\UserRepositoryInterface;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteDeviceControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const DELETE_DEVICE_URL = '/HomeApp/api/user/user-devices/%d';

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private UserRepositoryInterface $userRepository;

    private GroupRepositoryInterface $groupNameRepository;

    private DeviceRepositoryInterface $deviceRepository;

    private User $regularUserTwo;

    private User $adminUser;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userToken = $this->setUserToken($this->client);

        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->groupNameRepository = $this->entityManager->getRepository(Group::class);
        $this->regularUserTwo = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        $this->adminUser = $this->userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);

        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
    }

    public function test_regular_user_cannot_delete_device_group_not_apart_of(): void
    {
        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_ONE, UserDataFixtures::REGULAR_PASSWORD);
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $groupNameRepository = $this->entityManager->getRepository(Group::class);
        /** @var Group[] $groupsUserIsNotApartOf */
        $groupsUserIsNotApartOf = $groupNameRepository->findGroupsUserIsNotApartOf(
            $user,
            $user->getAssociatedGroupIDs(),
        );

        /** @var \App\Entity\Device\Devices[] $devices */
        $devices = $this->entityManager->getRepository(Devices::class)->findBy(['groupID' => $groupsUserIsNotApartOf]);

        if (empty($devices)) {
            self::fail('No device found to delete for testing');
        }

        $device = $devices[0];

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$userToken],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('You Are Not Authorised To Be Here', $responseData['title']);
        self::assertEquals('You have been denied permission to perform this action', $responseData['errors'][0]);

    }

//    Old functionality enable if only person who created device can delete it or admins
//    public function test_regular_user_cannot_delete_admin_device_group_is_apart_of(): void
//    {
//        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_TWO, UserDataFixtures::REGULAR_PASSWORD);
//        /** @var User $user */
//        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
//        $groupNameRepository = $this->entityManager->getRepository(GroupNames::class);
//        /** @var GroupNames[] $groupsUserIsApartOf */
//        $groupsUserIsApartOf = $groupNameRepository->findGroupsUserIsApartOf($user->getAssociatedgroupIDs(), $user);
//
//        if (empty($groupsUserIsApartOf)) {
//            self::fail('No group found for testing');
//        }
//        /** @var Devices[] $devices */
//        $devices = [];
//        foreach ($groupsUserIsApartOf as $groupUserIsNotApartOf) {
//            $devicesResult = $this->entityManager->getRepository(Devices::class)->findBy(['groupID' => $groupUserIsNotApartOf->getgroupID()]);
//            if ($devicesResult) {
//                $devices = array_merge($devicesResult, $devices);
//            }
//        }
//
//        foreach ($devices as $potentialDevice) {
//            if ($potentialDevice->getGroupNameObject()->getgroupID() !== $user->getgroupID()->getgroupID()) {
//                $device = $potentialDevice;
//                break;
//            }
//        }
//
//        if (empty($device)) {
//            self::fail('No device found to delete for testing');
//        }
//
//
//        $this->client->request(
//            Request::METHOD_DELETE,
//            sprintf(self::DELETE_DEVICE_URL, $device->getDeviceID()),
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$userToken],
//        );
//
//        $responseData = json_decode(
//            $this->client->getResponse()->getContent(),
//            true,
//            512,
//            JSON_THROW_ON_ERROR
//        );
//
//        self::assertEquals('You Are Not Authorised To Be Here', $responseData['title']);
//        self::assertEquals('You have been denied permission to perform this action', $responseData['errors'][0]);
//        self::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
//    }

    public function test_regular_user_can_delete_device_owner_of_response_only_payload(): void
    {
        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);

        /** @var \App\Entity\Device\Devices[] $devicesResult */
        $devicesResult = $this->deviceRepository->findBy(['createdBy' => $this->regularUserTwo]);
        self::assertNotEmpty($devicesResult);

        $device = $devicesResult[0];

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$userToken],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $deletedDevice = $this->deviceRepository->findOneBy(['deviceID' => $device->getDeviceID()]);
        self::assertNull($deletedDevice);

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertNotEmpty($responseData['payload']);

        self::assertEquals($device->getDeviceID(), $responseData['payload']['deviceID']);
        self::assertEquals($device->getDeviceName(), $responseData['payload']['deviceName']);
        self::assertEquals($device->getIpAddress(), $responseData['payload']['ipAddress']);
        self::assertEquals($device->getExternalIpAddress(), $responseData['payload']['externalIpAddress']);
        self::assertTrue($responseData['payload']['canEdit']);
        self::assertTrue($responseData['payload']['canDelete']);
        self::assertEquals(DeleteDeviceController::REQUEST_SUCCESSFUL, $responseData['title']);
    }

    public function test_admin_user_can_delete_device_response_only_payload(): void
    {
        /** @var \App\Entity\Device\Devices[] $devicesResult */
        $devicesResult = $this->deviceRepository->findAll();
        self::assertNotEmpty($devicesResult);

        $device = $devicesResult[0];

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '. $this->userToken],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $deletedDevice = $this->deviceRepository->findOneBy(['deviceID' => $device->getDeviceID()]);
        self::assertNull($deletedDevice);

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertNotEmpty($responseData['payload']);

        self::assertEquals($device->getDeviceID(), $responseData['payload']['deviceID']);
        self::assertEquals($device->getDeviceName(), $responseData['payload']['deviceName']);
        self::assertEquals($device->getIpAddress(), $responseData['payload']['ipAddress']);
        self::assertEquals($device->getExternalIpAddress(), $responseData['payload']['externalIpAddress']);
        self::assertTrue($responseData['payload']['canEdit']);
        self::assertTrue($responseData['payload']['canDelete']);
        self::assertEquals(\App\Controller\Device\DeleteDeviceController::REQUEST_SUCCESSFUL, $responseData['title']);
    }

    public function test_regular_user_can_delete_device_group_is_apart_of(): void
    {
        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_TWO, UserDataFixtures::REGULAR_PASSWORD);

        /** @var Group[] $groupsUserIsApartOf */
        $groupsUserIsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($this->regularUserTwo);

        if (empty($groupsUserIsApartOf)) {
            self::fail('No group found for testing');
        }
        /** @var \App\Entity\Device\Devices[] $devicesResult */
        $devicesResult = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);

        if (empty($devicesResult)) {
            self::fail('No device found to delete for testing');
        }

        $device = $devicesResult[0];

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$userToken],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $deletedDevice = $this->deviceRepository->findOneBy(['deviceID' => $device->getDeviceID()]);
        self::assertNull($deletedDevice);

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertNotEmpty($responseData['payload']);
    }


    public function test_admin_user_can_delete_device_not_apart_of(): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]);

        /** @var Group[] $groupsUserIsNotApartOf */
        $groupsUserIsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf(
            $user,
            $user->getAssociatedGroupIDs(),
        );

            /** @var \App\Entity\Device\Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);

        if (empty($devices)) {
            self::fail('No device found to delete for testing');
        }

        $device = $devices[0];
        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertNotEmpty($responseData['payload']);
        /** @var \App\Entity\Device\Devices $deletedDevice */
        $deletedDevice = $this->deviceRepository->findOneBy(['deviceID' => $device->getDeviceID()]);
        self::assertNull($deletedDevice);
    }

    public function test_deleting_device_that_doesnt_exist(): void
    {
        while (true) {
            $nonExistentDeviceID = random_int(1, 100000);

            /** @var \App\Entity\Device\Devices $device */
            $device = $this->deviceRepository->findOneBy(['deviceID' => $nonExistentDeviceID]);
            if (!$device instanceof Devices) {
                break;
            }
        }
        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_DEVICE_URL, $nonExistentDeviceID),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

//    /**
//     * @dataProvider wrongHttpsMethodDataProvider
//     */
//    public function test_deleting_device_wrong_http_method(string $httpVerb): void
//    {
//        $this->client->request(
//            $httpVerb,
//            sprintf(self::DELETE_DEVICE_URL, 1),
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
//        );
//
//        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
//    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_GET],
            [Request::METHOD_PUT],
            [Request::METHOD_PATCH],
            [Request::METHOD_POST],
        ];
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }
}
