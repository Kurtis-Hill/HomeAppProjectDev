<?php

namespace App\Tests\Controller\Device;

use App\Controller\Device\GetDeviceController;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Device\Devices;
use App\Entity\Sensor\Sensor;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Repository\Device\ORM\DeviceRepository;
use App\Repository\Sensor\Sensors\ORM\SensorRepository;
use App\Repository\User\ORM\GroupRepository;
use App\Repository\User\ORM\UserRepository;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\PaginationCalculator;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Tests\Controller\ControllerTestCase;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetDeviceControllerTest extends ControllerTestCase
{
    private const GET_SINGLE_DEVICE_URL = CommonURL::USER_HOMEAPP_API_URL . 'user-device/%d?responseType=%s';

    private const GET_ALL_DEVICES_URL = CommonURL::USER_HOMEAPP_API_URL . 'user-device?responseType=%s';

    private DeviceRepository $deviceRepository;

    private GroupRepository $groupNameRepository;

    private SensorRepository $sensorRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
        $this->groupNameRepository = $this->entityManager->getRepository(Group::class);
        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
    }

    public function test_get_device_admin_only_response(): void
    {
        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findAll();
        $device = $devices[0];

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            method: Request::METHOD_GET,
            uri: sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::FULL->value),
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertDeviceIsSameAsExpected($device, $payload);
        self::assertTrue($payload['canEdit']);
        self::assertTrue($payload['canDelete']);
        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);
    }

    public function test_get_device_admin_full_response(): void
    {
        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findAll();
        foreach ($devices as $deviceToTest) {
            $sensors = $this->sensorRepository->findBy(['deviceID' => $deviceToTest->getDeviceID()]);
            if (!empty($sensors)) {
                $device = $deviceToTest;
            }
        }
        if (!isset($device)) {
            self::fail('No device with sensors found');
        }

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            method: Request::METHOD_GET,
            uri: sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::FULL->value),
            parameters: [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);
        self::assertDeviceIsSameAsExpected($device, $payload);;
        self::assertTrue($payload['canEdit']);
        self::assertTrue($payload['canDelete']);

        $createdByResponse = $payload['createdBy'];
        $createdByUser = $device->getCreatedBy();

        self::assertUserIsSameAsExpected($createdByUser, $createdByResponse);;

        self::assertArrayHasKey('sensorData', $payload);
        $sensorData = $payload['sensorData'];

        $sensorIDs = array_column($sensorData, 'sensorID');

        $deviceSensors = $this->sensorRepository->findBy(['deviceID' => $device->getDeviceID()]);
        foreach ($deviceSensors as $sensor) {
            if (!in_array($sensor->getSensorID(), $sensorIDs, true)) {
                self::fail('Sensor not found in response');
            }
        }

        foreach ($sensorData as $data) {
            self::assertArrayHasKey('sensorID', $data);
            self::assertArrayHasKey('sensorName', $data);
            self::assertArrayHasKey('createdBy', $data);
            self::assertArrayHasKey('device', $data);
            self::assertArrayHasKey('sensorType', $data);
            self::assertArrayHasKey('sensorReadingTypes', $data);
        }
    }

    public function test_get_device_of_group_user_is_not_assigned_to_regular_user(): void
    {
        $groupsUserIsNotAssignedTo = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->regularUserOne);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }

        $device = $devices[0];

        $this->authenticateRegularUserOne();
        $this->client->jsonRequest(
            method: Request::METHOD_GET,
            uri: sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::FULL->value),
        );
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function test_get_device_of_group_user_is_not_assigned_to_admin(): void
    {
        $groupsUserIsNotAssignedTo = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->adminOne);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }
        $device = $devices[0];

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            method: Request::METHOD_GET,
            uri: sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::FULL->value),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];

        self::assertNotEmpty($payload);
    }

    public function test_get_device_of_group_user_is_assigned_to_regular_user_full_response(): void
    {
        $groupsUserIsAssignedTo = $this->groupNameRepository->findGroupsUserIsApartOf($this->regularUserOne);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }
        $device = $devices[0];

        $this->authenticateRegularUserOne();
        $this->client->jsonRequest(
            method: Request::METHOD_GET,
            uri: sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::FULL->value),
            parameters: [RequestQueryParameterHandler::RESPONSE_TYPE => 'full'],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertDeviceIsSameAsExpected($device, $payload);;
        self::assertTrue($payload['canEdit']);
        self::assertTrue($payload['canDelete']);

        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);

        $createdByResponse = $payload['createdBy'];
        $createdByUser = $device->getCreatedBy();

        self::assertUserIsSameAsExpected($createdByUser, $createdByResponse);;;

        $deviceSensors = $this->sensorRepository->findBy(['deviceID' => $device->getDeviceID()]);

        self::assertArrayHasKey('sensorData', $payload);
        $sensorData = $payload['sensorData'];

        $sensorIDs = array_column($sensorData, 'sensorID');

        foreach ($deviceSensors as $sensor) {
            if (!in_array($sensor->getSensorID(), $sensorIDs, true)) {
                self::fail('Sensor not found in response');
            }
        }

        foreach ($sensorData as $data) {
            self::assertArrayHasKey('sensorID', $data);
            self::assertArrayHasKey('sensorName', $data);
            self::assertArrayHasKey('createdBy', $data);
            self::assertArrayHasKey('device', $data);
            self::assertArrayHasKey('sensorType', $data);
            self::assertArrayHasKey('sensorReadingTypes', $data);
        }
    }

    public function test_get_device_of_group_user_is_assigned_to_regular_user_response_type_only(): void
    {
        $groupsUserIsAssignedTo = $this->groupNameRepository->findGroupsUserIsApartOf($this->regularUserOne);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }
        $device = $devices[0];

        $this->authenticateRegularUserOne();
        $this->client->jsonRequest(
            method: Request::METHOD_GET,
            uri: sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::FULL->value),
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);
        self::assertDeviceIsSameAsExpected($device, $payload);
    }

    public function test_get_device_of_group_user_is_assigned_to_admin(): void
    {
        $groupsUserIsAssignedTo = $this->groupNameRepository->findGroupsUserIsApartOf($this->adminOne);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }
        $device = $devices[0];

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            method: Request::METHOD_GET,
            uri: sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::FULL->value),
            parameters: [RequestQueryParameterHandler::RESPONSE_TYPE => 'full'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);
        $title = $responseData['title'];
        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);
    }

    public function test_get_all_devices_doesnt_return_devices_of_group_user_is_not_assigned_to_regular_user(): void
    {
        $groupsUserIsAssignedTo = $this->groupNameRepository->findGroupsUserIsApartOf($this->regularUserOne);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }

        $this->authenticateRegularUserOne();
        $this->client->jsonRequest(
            method: Request::METHOD_GET,
            uri: sprintf(sprintf(self::GET_ALL_DEVICES_URL, RequestTypeEnum::FULL->value), RequestTypeEnum::FULL->value),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);

        $allDevices = $this->deviceRepository->findAll();

        foreach ($allDevices as $device) {
            if ($device->getGroupObject()->getGroupID() === $groupsUserIsAssignedTo) {
                self::assertContains($device->getDeviceID(), $payload);
            }
            if ($device->getGroupObject()->getGroupID() !== $groupsUserIsAssignedTo) {
                self::assertNotContains($device->getDeviceID(), $payload);
            }
        }
    }

    public function test_get_all_devices_returns_all_devices_admin(): void
    {
        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            method: Request::METHOD_GET,
            uri: sprintf(sprintf(self::GET_ALL_DEVICES_URL, RequestTypeEnum::FULL->value), RequestTypeEnum::FULL->value),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);

        $allDevices = $this->deviceRepository->findAll();

        $deviceIDs = array_column($payload, 'deviceID');
        foreach ($allDevices as $device) {
            self::assertContains($device->getDeviceID(), $deviceIDs, $device->getDeviceName());
        }
    }

    /**
     * @dataProvider limitAndPageDataProvider
     */
    public function test_limit_and_page_works_admin_user(int $limit, int $page): void
    {
        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            method: Request::METHOD_GET,
            uri: sprintf(self::GET_ALL_DEVICES_URL, RequestTypeEnum::FULL->value) . '&limit=' . $limit . '&page=' . $page,
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];
        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);
        if (is_array($payload)) {
            self::assertCount($limit, $payload);
            $deviceIds = array_column($payload, 'deviceID');

            $devices = $this->deviceRepository->findBy(
                [],
                ['deviceName' => 'ASC'],
            );
            $devicesThatShouldBeReturned = array_slice($devices, PaginationCalculator::calculateOffset($limit, $page), $limit);
            $devicesThatShouldNotBeReturned = array_slice($devices, $page * 2);
            foreach ($devicesThatShouldBeReturned as $device) {
                self::assertContains($device->getDeviceID(), $deviceIds);
            }
            foreach ($devicesThatShouldNotBeReturned as $device) {
                self::assertNotContains($device->getDeviceID(), $deviceIds);
            }
        } else {
            self::assertEquals(GetDeviceController::NO_RESPONSE_MESSAGE, $payload);
        }
    }

    public function limitAndPageDataProvider(): Generator
    {
        yield [
            'limit' => 1,
            'page' => 1,
        ];

        yield [
            'limit' => 2,
            'page' => 1,
        ];

        yield [
            'limit' => 2,
            'page' => 1,
        ];

        yield [
            'limit' => 2,
            'page' => 2,
        ];

        yield [
            'limit' => 3,
            'page' => 4,
        ];

        //@TODO fix this test needs to be smarter and calculate expected size
//        yield [
//            'limit' => 4,
//            'page' => 3,
//        ];
    }

    // Didnt think it was worth maintaining
//    /**
//     * @dataProvider limitAndPageWrongDataProvider
//     */
//    public function test_limit_and_page_incorrect_data_types_admin_user(mixed $limit, mixed $page, array $message = []): void
//    {
//        $this->authenticateAdminOne();
//        $this->client->jsonRequest(
//            method: Request::METHOD_GET,
//            uri: self::GET_ALL_DEVICES_URL . '?limit=' . $limit . '&page=' . $page,
//            parameters: ['limit' => $limit, 'page' => $page],
//        );
//
//        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//        $title = $responseData['title'];
//        if (empty($responseData['errors'])) {
//            self::fail('No errors or payload returned');
//        }
//        $errors = $responseData['errors'];
//
//        self::assertEquals($message, $errors);
//        self::assertValidationErrorMessage($title);
//    }
//
//    public function limitAndPageWrongDataProvider(): Generator
//    {
//        yield [
//            'limit' => [],
//            'page' => 10,
//            'messages' => [
//                'limit' => 'This value should be of type int.'
//            ],
//        ];
//
//        yield [
//            'limit' => 2,
//            'page' => [],
//            'messages' => [
//                'page' => 'This value should be of type int.'
//            ],
//        ];
//
//        yield [
//            'limit' => 'string',
//            'page' => 1,
//            'messages' => [
//                'limit' => 'This value should be of type int.'
//            ],
//        ];
//
//        yield [
//            'limit' => 2,
//            'page' => 'string',
//            'messages' => [
//                'page' => 'This value should be of type int.'
//            ],
//        ];
//
//        yield [
//            'limit' => false,
//            'page' => 4,
//            'messages' => [
//                'limit' => 'This value should be of type int.'
//            ],
//        ];
//
//        // true counts as 1 which is valid
////        yield [
////            'limit' => true,
////            'page' => 3,
////            'messages' => [
////                'limit must be an int|null you have provided "1"',
////            ],
////        ];
//
//        yield [
//            'limit' => [],
//            'page' => [],
//            'messages' => [
//                'page' => 'This value should be of type int.',
//                'limit' => 'This value should be of type int.',
//            ],
//        ];
//    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_getting_device_wrong_http_method_single(string $httpVerb): void
    {
        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            method: $httpVerb,
            uri: self::GET_SINGLE_DEVICE_URL,
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_getting_device_wrong_http_method_all(string $httpVerb): void
    {
        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            method: $httpVerb,
            uri: self::GET_SINGLE_DEVICE_URL,
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_DELETE],
            [Request::METHOD_PUT],
            [Request::METHOD_PATCH],
            [Request::METHOD_POST],
        ];
    }
}
