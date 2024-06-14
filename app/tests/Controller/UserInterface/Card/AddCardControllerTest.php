<?php

namespace App\Tests\Controller\UserInterface\Card;

use App\Controller\UserInterface\Card\AddCardController;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Device\Devices;
use App\Entity\Sensor\Sensor;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Entity\UserInterface\Card\CardState;
use App\Entity\UserInterface\Card\CardView;
use App\Entity\UserInterface\Card\Colour;
use App\Entity\UserInterface\Icons;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Repository\User\ORM\GroupRepositoryInterface;
use App\Repository\UserInterface\ORM\CardRepositories\CardColourRepository;
use App\Repository\UserInterface\ORM\CardRepositories\CardStateRepository;
use App\Repository\UserInterface\ORM\CardRepositories\CardViewRepository;
use App\Repository\UserInterface\ORM\IconsRepositoryInterface;
use App\Services\UserInterface\Cards\CardCreation\CardCreationHandler;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddCardControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const ADD_NEW_CARD =  '/HomeApp/api/user/card/add';

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private User $adminUserTwo;

    private User $regularUser;

    private GroupRepositoryInterface $groupRepository;

    private SensorRepositoryInterface $sensorRepository;

    private DeviceRepositoryInterface $deviceRepository;

    private CardViewRepository $cardViewRepository;

    private IconsRepositoryInterface $iconsRepository;

    private CardColourRepository $cardColourRepository;

    private CardStateRepository $cardStateRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userToken = $this->setUserToken($this->client);

        $this->regularUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $this->adminUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]);

        $this->groupRepository = $this->entityManager->getRepository(Group::class);
        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
        $this->cardViewRepository = $this->entityManager->getRepository(CardView::class);
        $this->iconsRepository = $this->entityManager->getRepository(Icons::class);
        $this->cardColourRepository = $this->entityManager->getRepository(Colour::class);
        $this->cardStateRepository = $this->entityManager->getRepository(CardState::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;

        parent::tearDown();
    }

    /**
    * @dataProvider sendingIncorrectDataTypesProvider
     */
    public function test_sending_incorrect_data_types(
        mixed $sensorID,
        mixed $cardIcon,
        mixed $cardColour,
        mixed $cardState,
        array $errorMessages,
    ): void {
        $formData = [
            'sensorID' => $sensorID,
            'cardIcon' => $cardIcon,
            'cardColour' => $cardColour,
            'cardState' => $cardState,
        ];

        $this->client->request(
            method: Request::METHOD_POST,
            uri: self::ADD_NEW_CARD,
            server: ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken],
            content: json_encode($formData)
        );

        $response = $this->client->getResponse();
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseContent = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $responseErrors = $responseContent['errors'];

        self::assertEquals($errorMessages, $responseErrors);
    }

    public function sendingIncorrectDataTypesProvider(): Generator
    {
        yield [
            'sensorID' => [],
            'cardIcon' => 1,
            'cardColour' => 2,
            'cardState' => 3,
            'errorMessages' => [
                'sensorID must be an int|null you have provided array',
            ],
        ];

        yield [
            'sensorID' => 1,
            'cardIcon' => [],
            'cardColour' => 2,
            'cardState' => 3,
            'errorMessages' => [
                'cardIcon must be an int|null you have provided array',
            ],
        ];

        yield [
            'sensorID' => 1,
            'cardIcon' => 2,
            'cardColour' => [],
            'cardState' => 3,
            'errorMessages' => [
                'cardColour must be an int|null you have provided array',
            ],
        ];

        yield [
            'sensorID' => 1,
            'cardIcon' => 2,
            'cardColour' => 3,
            'cardState' => [],
            'errorMessages' => [
                'cardState must be an int|null you have provided array',
            ],
        ];

        yield [
            'sensorID' => [],
            'cardIcon' => [],
            'cardColour' => [],
            'cardState' => [],
            'errorMessages' => [
                'sensorID must be an int|null you have provided array',
                'cardIcon must be an int|null you have provided array',
                'cardColour must be an int|null you have provided array',
                'cardState must be an int|null you have provided array',
            ],
        ];

        yield [
            'sensorID' => 'string',
            'cardIcon' => 2,
            'cardColour' => 3,
            'cardState' => 4,
            'errorMessages' => [
                'sensorID must be an int|null you have provided "string"',
            ],
        ];

        yield [
            'sensorID' => 1,
            'cardIcon' => 'string',
            'cardColour' => 3,
            'cardState' => 4,
            'errorMessages' => [
                'cardIcon must be an int|null you have provided "string"',
            ],
        ];

        yield [
            'sensorID' => 1,
            'cardIcon' => 2,
            'cardColour' => 'string',
            'cardState' => 4,
            'errorMessages' => [
                'cardColour must be an int|null you have provided "string"',
            ],
        ];

        yield [
            'sensorID' => 1,
            'cardIcon' => 2,
            'cardColour' => 3,
            'cardState' => 'string',
            'errorMessages' => [
                'cardState must be an int|null you have provided "string"',
            ],
        ];
    }

    public function test_sending_request_regular_user_sensor_group_not_apart_of(): void
    {
        /** @var Group $groupsRegularUserIsNotApartOf */
        $groupsRegularUserIsNotApartOf = $this->groupRepository->findGroupsUserIsNotApartOf($this->regularUser);

        /** @var Devices[] $devicesInGroupsUserNotApartOf */
        $devicesInGroupsUserNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsRegularUserIsNotApartOf]);
        $sensor = null;
        foreach ($devicesInGroupsUserNotApartOf as $device) {
            $sensor = $this->sensorRepository->findOneBy(['deviceID' => $device]);
            if ($sensor !== null) {
                break;
            }
        }

        self::assertNotNull($sensor);

        $formData = [
            'sensorID' => $sensor->getSensorID(),
            'cardIcon' => 1,
            'cardColour' => 2,
            'cardState' => 3,
        ];

        $userToken = $this->setUserToken(
            $this->client,
            $this->regularUser->getEmail(),
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->request(
            method: Request::METHOD_POST,
            uri: self::ADD_NEW_CARD,
            server: ['HTTP_AUTHORIZATION' => 'Bearer ' . $userToken],
            content: json_encode($formData)
        );
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $responseContent = $this->client->getResponse()->getContent();

        $jsonResponse = json_decode($responseContent, true, 512, JSON_THROW_ON_ERROR);

        $title = $jsonResponse['title'];
        self::assertEquals(AddCardController::NOT_AUTHORIZED_TO_BE_HERE, $title);

        self::assertEquals(\App\Controller\UserInterface\Card\AddCardController::NO_RESPONSE_MESSAGE, $jsonResponse['errors'][0]);
    }

    public function test_sending_request_admin_user_sensor_group_not_apart_of(): void
    {
        /** @var \App\Entity\User\Group $groupsRegularUserIsNotApartOf */
        $groupsRegularUserIsNotApartOf = $this->groupRepository->findGroupsUserIsNotApartOf($this->adminUserTwo);

        /** @var Devices[] $devicesInGroupsUserNotApartOf */
        $devicesInGroupsUserNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsRegularUserIsNotApartOf]);

        $sensor = null;
        foreach ($devicesInGroupsUserNotApartOf as $device) {
            $sensor = $this->sensorRepository->findOneBy(['deviceID' => $device]);
            if ($sensor !== null) {
                $cardView = $this->cardViewRepository->findOneBy(['sensor' => $sensor, 'userID' => $this->adminUserTwo]);
                if ($cardView === null) {
                    break;
                }
            }
        }
        self::assertNotNull($sensor);

        /** @var Icons[] $icons */
        $icons = $this->iconsRepository->findAll();
        $icon = $icons[0];

        /** @var Colour[] $colours */
        $colours = $this->cardColourRepository->findAll();
        $colour = $colours[0];

        /** @var CardState[] $states */
        $states = $this->cardStateRepository->findAll();
        $state = $states[0];

        $formData = [
            'sensorID' => $sensor->getSensorID(),
            'cardIcon' => $icon->getIconID(),
            'cardColour' => $colour->getColourID(),
            'cardState' => $state->getStateID(),
        ];

        $userToken = $this->setUserToken(
            $this->client,
            $this->adminUserTwo->getEmail(),
        );

        $this->client->request(
            method: Request::METHOD_POST,
            uri: self::ADD_NEW_CARD,
            server: ['HTTP_AUTHORIZATION' => 'Bearer ' . $userToken],
            content: json_encode($formData)
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->client->getResponse()->getContent();

        $jsonResponse = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        $title = $jsonResponse['title'];
        self::assertEquals(AddCardController::REQUEST_SUCCESSFUL, $title);

        $payload = $jsonResponse['payload'];
        self::assertEquals(AddCardController::NO_RESPONSE_MESSAGE, $payload);

        /** @var CardView $cardView */
        $cardView = $this->cardViewRepository->findOneBy(['sensor' => $sensor->getSensorID(), 'userID' => $this->adminUserTwo]);
        self::assertNotNull($cardView);

        self::assertEquals($icon->getIconID(), $cardView->getCardIconID()->getIconID());
        self::assertEquals($colour->getColourID(), $cardView->getCardColourID()->getColourID());
        self::assertEquals($state->getStateID(), $cardView->getCardStateID()->getStateID());
    }

    /**
     * @dataProvider missingRequestPartsProvider
     */
    public function test_sending_missing_request_parts_check_sensor_is_created_with_random(
        bool $cardIcon,
        bool $cardColour,
        bool $cardState,
    ): void {
        /** @var \App\Entity\User\Group $groupsRegularUserIsNotApartOf */
        $groupsRegularUserIsNotApartOf = $this->groupRepository->findGroupsUserIsNotApartOf($this->adminUserTwo);

        /** @var Devices[] $devicesInGroupsUserNotApartOf */
        $devicesInGroupsUserNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsRegularUserIsNotApartOf]);

        $sensor = null;
        foreach ($devicesInGroupsUserNotApartOf as $device) {
            $sensor = $this->sensorRepository->findOneBy(['deviceID' => $device]);
            if ($sensor !== null) {
                $cardView = $this->cardViewRepository->findOneBy(['sensor' => $sensor, 'userID' => $this->adminUserTwo]);
                if ($cardView === null) {
                    break;
                }
            }
        }
        self::assertNotNull($sensor);

        if ($cardIcon === true) {
            $icon = $this->iconsRepository->findAll()[0];
        }
        /** @var Icons|null $icon */
        $icon = $icon ?? null;

        if ($cardColour === true) {
            $colour = $this->cardColourRepository->findAll()[0];
        }
        /** @var Colour|null $cardColour */
        $colour = $colour ?? null;

        if ($cardState === true) {
            $state = $this->cardStateRepository->findAll()[0];
        }
        /** @var CardState|null $cardState */
        $state = $state ?? null;


        $formData = [
            'sensorID' => $sensor->getSensorID(),
            'cardIcon' => $icon?->getIconID(),
            'cardColour' => $colour?->getColourID(),
            'cardState' => $state?->getStateID(),
        ];

        $userToken = $this->setUserToken(
            $this->client,
            $this->adminUserTwo->getEmail(),
        );

        $this->client->request(
            method: Request::METHOD_POST,
            uri: self::ADD_NEW_CARD,
            server: ['HTTP_AUTHORIZATION' => 'Bearer ' . $userToken],
            content: json_encode($formData)
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->client->getResponse()->getContent();

        $jsonResponse = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        $title = $jsonResponse['title'];

        self::assertEquals(AddCardController::REQUEST_SUCCESSFUL, $title);

        $payload = $jsonResponse['payload'];

        self::assertEquals(\App\Controller\UserInterface\Card\AddCardController::NO_RESPONSE_MESSAGE, $payload);

        /** @var CardView $cardView */
        $cardView = $this->cardViewRepository->findOneBy(['sensor' => $sensor->getSensorID(), 'userID' => $this->adminUserTwo]);
        self::assertNotNull($cardView);


        if ($cardIcon === true) {
            self::assertEquals($icon->getIconID(), $cardView->getCardIconID()->getIconID());
        } else {
            self::assertNotNull($cardView->getCardIconID());
        }

        if ($cardColour === true) {
            self::assertEquals($colour->getColourID(), $cardView->getCardColourID()->getColourID());
        } else {
            self::assertNotNull($cardView->getCardColourID());
        }

        if ($cardState === true) {
            self::assertEquals($state->getStateID(), $cardView->getCardStateID()->getStateID());
        } else {
            self::assertNotNull($cardView->getCardStateID());
        }
    }

    public function missingRequestPartsProvider(): Generator
    {
        yield [
            'cardIcon' => false,
            'cardColour' => true,
            'cardState' => true,
        ];

        yield [
            'cardIcon' => true,
            'cardColour' => false,
            'cardState' => true,
        ];

        yield [
            'cardIcon' => true,
            'cardColour' => true,
            'cardState' => false,
        ];

        yield [
            'cardIcon' => false,
            'cardColour' => false,
            'cardState' => true,
        ];

        yield [
            'cardIcon' => false,
            'cardColour' => true,
            'cardState' => false,
        ];

        yield [
            'cardIcon' => true,
            'cardColour' => false,
            'cardState' => false,
        ];

        yield [
            'cardIcon' => false,
            'cardColour' => false,
            'cardState' => false,
        ];

        yield [
            'cardIcon' => true,
            'cardColour' => true,
            'cardState' => true,
        ];
    }

    public function test_sending_request_for_cardview_that_already_exists(): void
    {
        $cardViewThatAlreadyExists = $this->cardViewRepository->findOneBy(['userID' => $this->adminUserTwo]);

        $formData = [
            'sensorID' => $cardViewThatAlreadyExists->getSensor()->getSensorID(),
            'cardIcon' => $cardViewThatAlreadyExists->getCardIconID()->getIconID(),
            'cardColour' => $cardViewThatAlreadyExists->getCardColourID()->getColourID(),
            'cardState' => $cardViewThatAlreadyExists->getCardStateID()->getStateID(),
        ];

        $userToken = $this->setUserToken(
            $this->client,
            $this->adminUserTwo->getEmail(),
        );

        $this->client->request(
            method: Request::METHOD_POST,
            uri: self::ADD_NEW_CARD,
            server: ['HTTP_AUTHORIZATION' => 'Bearer ' . $userToken],
            content: json_encode($formData)
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $response = $this->client->getResponse()->getContent();

        $jsonResponse = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        $title = $jsonResponse['title'];
        self::assertEquals(AddCardController::BAD_REQUEST_NO_DATA_RETURNED, $title);

        $payload = $jsonResponse['errors'];
        self::assertEquals(CardCreationHandler::SENSOR_ALREADY_EXISTS, $payload[0]);
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::ADD_NEW_CARD,
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
            [Request::METHOD_DELETE],
        ];
    }
}
