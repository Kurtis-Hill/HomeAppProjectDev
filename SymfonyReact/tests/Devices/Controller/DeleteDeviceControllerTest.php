<?php

namespace App\Tests\Devices\Controller;

use App\Devices\Controller\DeleteDeviceController;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Authentication\Entity\GroupNameMapping;
use App\Devices\Entity\Devices;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\GroupNames;
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

    private const DELETE_DEVICE_URL = '/HomeApp/api/user/user-devices/%d/delete';

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userToken = $this->setUserToken($this->client);
    }

    public function test_regular_user_cannot_delete_device_group_not_apart_of(): void
    {
        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_ONE, UserDataFixtures::REGULAR_PASSWORD);
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $groupNameRepository = $this->entityManager->getRepository(GroupNames::class);
        /** @var GroupNames[] $groupsUserIsNotApartOf */
        $groupsUserIsNotApartOf = $groupNameRepository->findGroupsUserIsNotApartOf(
            $user,
            $user->getAssociatedGroupNameIds(),
        );

        /** @var Devices[] $devices */
        $devices = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $groupsUserIsNotApartOf]);

        if (empty($devices)) {
            self::fail('No device found to delete for testing');
        }

        foreach ($devices as $device) {
            $this->client->request(
                Request::METHOD_DELETE,
                sprintf(self::DELETE_DEVICE_URL, $device->getDeviceID()),
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
            self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        }
    }

//    Old functionality enable if only person who created device can delete it or admins
//    public function test_regular_user_cannot_delete_admin_device_group_is_apart_of(): void
//    {
//        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_TWO, UserDataFixtures::REGULAR_PASSWORD);
//        /** @var User $user */
//        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
//        $groupNameRepository = $this->entityManager->getRepository(GroupNames::class);
//        /** @var GroupNames[] $groupsUserIsApartOf */
//        $groupsUserIsApartOf = $groupNameRepository->findGroupsUserIsApartOf($user->getAssociatedGroupNameIds(), $user);
//
//        if (empty($groupsUserIsApartOf)) {
//            self::fail('No group found for testing');
//        }
//        /** @var Devices[] $devices */
//        $devices = [];
//        foreach ($groupsUserIsApartOf as $groupUserIsNotApartOf) {
//            $devicesResult = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $groupUserIsNotApartOf->getGroupNameID()]);
//            if ($devicesResult) {
//                $devices = array_merge($devicesResult, $devices);
//            }
//        }
//
//        foreach ($devices as $potentialDevice) {
//            if ($potentialDevice->getGroupNameObject()->getGroupNameID() !== $user->getGroupNameID()->getGroupNameID()) {
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


    public function test_regular_user_can_delete_device_group_is_apart_of(): void
    {
        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_TWO, UserDataFixtures::REGULAR_PASSWORD);

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);

        $groupNameRepository = $this->entityManager->getRepository(GroupNames::class);
        /** @var GroupNames[] $groupsUserIsApartOf */
        $groupsUserIsApartOf = $groupNameRepository->findGroupsUserIsApartOf($user->getAssociatedGroupNameIds(), $user);

        if (empty($groupsUserIsApartOf)) {
            self::fail('No group found for testing');
        }
        /** @var Devices[] $devicesResult */
        $devicesResult = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $groupsUserIsApartOf]);

        if (empty($devicesResult)) {
            self::fail('No device found to delete for testing');
        }

        foreach ($devicesResult as $device) {
            $this->client->request(
                Request::METHOD_DELETE,
                sprintf(self::DELETE_DEVICE_URL, $device->getDeviceID()),
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

            self::assertEquals($device->getDeviceName(), $responseData['payload']['deviceName']);
            self::assertEquals($device->getGroupNameObject()->getGroupNameID(), $responseData['payload']['groupNameID']);
            self::assertEquals($device->getRoomObject()->getRoomID(), $responseData['payload']['roomID']);
            self::assertEquals($device->getCreatedBy()->getUserIdentifier(), $responseData['payload']['createdBy']);
            self::assertEquals($device->getIpAddress(), $responseData['payload']['ipAddress']);
            self::assertEquals($device->getExternalIpAddress(), $responseData['payload']['externalIpAddress']);
            self::assertEquals($device->getRoles(), $responseData['payload']['roles']);
            self::assertNull($responseData['payload']['secret']);


            self::assertEquals(DeleteDeviceController::REQUEST_SUCCESSFUL, $responseData['title']);
            self::assertResponseStatusCodeSame(Response::HTTP_OK);
        }
    }


    public function test_admin_user_can_delete_device_not_apart_of(): void
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]);

        $groupNameRepository = $this->entityManager->getRepository(GroupNames::class);
        /** @var GroupNames[] $groupsUserIsNotApartOf */
        $groupsUserIsNotApartOf = $groupNameRepository->findGroupsUserIsNotApartOf(
            $user,
            $user->getAssociatedGroupNameIds(),
        );

            /** @var Devices[] $devices */
        $devices = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $groupsUserIsNotApartOf]);

        if (empty($devices)) {
            self::fail('No device found to delete for testing');
        }

        foreach ($devices as $device) {
            $this->client->request(
                Request::METHOD_DELETE,
                sprintf(self::DELETE_DEVICE_URL, $device->getDeviceID()),
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

            /** @var Devices $deletedDevice */
            $deletedDevice = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceID' => $device->getDeviceID()]);

            self::assertNull($deletedDevice);
            self::assertEquals(DeleteDeviceController::REQUEST_SUCCESSFUL, $responseData['title']);
            self::assertIsArray($responseData['payload']);
            self::assertEquals($device->getDeviceName(), $responseData['payload']['deviceName']);
            self::assertEquals($device->getGroupNameObject()->getGroupNameID(), $responseData['payload']['groupNameID']);
            self::assertEquals($device->getRoomObject()->getRoomID(), $responseData['payload']['roomID']);
            self::assertEquals($device->getCreatedBy()->getUserIdentifier(), $responseData['payload']['createdBy']);
            self::assertEquals($device->getIpAddress(), $responseData['payload']['ipAddress']);
            self::assertEquals($device->getExternalIpAddress(), $responseData['payload']['externalIpAddress']);
            self::assertEquals($device->getRoles(), $responseData['payload']['roles']);
            self::assertNull($responseData['payload']['secret']);
            self::assertResponseStatusCodeSame(Response::HTTP_OK);
        }
    }

    public function test_deleting_device_that_doesnt_exist(): void
    {
        $deviceRepository = $this->entityManager->getRepository(Devices::class);
        while (true) {
            $nonExistentDeviceID = random_int(1, 100000);

            /** @var Devices $device */
            $device = $deviceRepository->findOneBy(['deviceID' => $nonExistentDeviceID]);
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

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_deleting_device_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            sprintf(self::DELETE_DEVICE_URL, 1),
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
