<?php

namespace UserInterface\Controller\Card;

use App\Doctrine\DataFixtures\Core\UserDataFixtures;
use App\Authentication\Controller\SecurityController;
use App\Common\API\APIErrorMessages;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use App\User\Entity\User;
use App\UserInterface\Entity\Card\CardColour;
use App\UserInterface\Entity\Card\Cardstate;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Entity\Icons;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CardViewFormControllerTest extends WebTestCase
{
    private const GET_CARD_FORM_URL =  '/HomeApp/api/user/card-form-data/get/%d';

    private const UPDATE_CARD_FORM_URL =  '/HomeApp/api/user/card-form-data/update/%d';

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userToken = $this->setUserToken();
    }

    private function setUserToken(bool $forceToken = false, string $username = null, string $password = null): string
    {
        $username = $username ?? UserDataFixtures::ADMIN_USER;
        $password = $password ?? UserDataFixtures::ADMIN_PASSWORD;

        if ($this->userToken === null || $forceToken === true) {
            $this->client->request(
                Request::METHOD_POST,
                SecurityController::API_USER_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"'.$username.'","password":"'.$password.'"}'
            );

            $requestResponse = $this->client->getResponse();
            $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            return $responseData['token'];
        }

        return $this->userToken;
    }

    /**
     * @dataProvider getCardViewFormDataProvider
     */
    public function testGetCardViewFormData(string $sensorType): void
    {
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $sensor = $this->entityManager->getRepository(Sensor::class)->findBy(['sensorTypeID' => $sensorType->getSensorTypeID()])[0];
        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findBy(['sensorNameID' => $sensor->getSensorNameID()])[0];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_CARD_FORM_URL, $cardViewObject->getCardViewID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );
        $responseContent = $this->client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true)['payload'];

        self::assertNotEmpty($responseData['sensorData']);

        foreach ($responseData['sensorData'] as $sensorData) {
            self::assertArrayHasKey('readingType', $sensorData);
            self::assertIsString($sensorData['readingType']);

            self::assertArrayHasKey('highReading', $sensorData);
            self::assertIsNumeric($sensorData['highReading']);

            self::assertArrayHasKey('lowReading', $sensorData);
            self::assertIsNumeric($sensorData['lowReading']);

            self::assertArrayHasKey('constRecord', $sensorData);
            self::assertIsBool($sensorData['constRecord']);
        }


        $allIcons = $this->entityManager->getRepository(Icons::class)->findAll();
        $allCardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
        $allCardState = $this->entityManager->getRepository(Cardstate::class)->findAll();

        self::assertEquals($cardViewObject->getCardViewID(), $responseData['cardViewID']);

        self::assertEquals($cardViewObject->getCardIconID()->getIconID(), $responseData['cardIcon']['iconID']);
        self::assertCount(count($allIcons), $responseData['iconSelection']);

        self::assertCount(count($allCardState), $responseData['userCardViewSelections']);

        self::assertEquals($cardViewObject->getCardColourID()->getColourID(), $responseData['cardColour']['colourID']);
        self::assertCount(count($allCardColours), $responseData['userColourSelections']);

        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function getCardViewFormDataProvider(): Generator
    {
        yield ['Dht'];

        yield ['Bmp'];

        yield ['Soil'];

        yield ['Dallas'];
    }

    /**
     * @dataProvider getCardViewFormIncorrectCardViewIDDataProvider
     */
    public function testGetCardViewFormIncorrectCardViewID(mixed $cardViewID): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_CARD_FORM_URL, $cardViewID),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function getCardViewFormIncorrectCardViewIDDataProvider(): Generator
    {
        yield [
            'notAInt',
        ];

        yield [
            null
        ];
    }

    public function testGetCardViewFormNoneExistentCardViewID(): void
    {
        while (true) {
            $randomNumber = random_int(1, 100000);
            $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['cardViewID' => $randomNumber]);

            if (!$cardView instanceof CardView) {
                break;
            }
        }
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_CARD_FORM_URL, $randomNumber),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        $responseContent = $this->client->getResponse()->getContent();

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider userCannotEditOtherUsersCardsDataProvider
     */
    public function testUserCannotEditOtherUsersCards(string $username, string $password, $usersCardToAlter): void
    {
        $userToken = $this->setUserToken(true, $username, $password);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $usersCardToAlter]);
        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findBy(['userID' => $user->getUserID()])[0];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_CARD_FORM_URL, $cardViewObject->getCardViewID()),
            ['card-view-id' => $cardViewObject->getCardViewID()],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        $responseContent = $this->client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        self::assertEquals(APIErrorMessages::ACCESS_DENIED, $responseData['errors'][0]);
        self::assertEquals('You Are Not Authorised To Be Here', $responseData['title']);
        self::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function userCannotEditOtherUsersCardsDataProvider(): Generator
    {
        yield [
            'username' => UserDataFixtures::REGULAR_USER,
            'password' => UserDataFixtures::REGULAR_PASSWORD,
            'alter' => UserDataFixtures::ADMIN_USER,
        ];

        yield [
            'username' => UserDataFixtures::ADMIN_USER,
            'password' => UserDataFixtures::ADMIN_PASSWORD,
            'alter' => UserDataFixtures::REGULAR_USER,
        ];
    }

    // Start of CardViewController::getCardViewForm() tests
    /**
     * @dataProvider malformedRequestDataProvider
     */
    public function testSendingMalformedRequest(array $requestData, string $errorMessage): void
    {
        $jsonRequestData = json_encode($requestData);
        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_CARD_FORM_URL, $requestData['cardViewID']),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonRequestData
        );

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function malformedRequestDataProvider(): Generator
    {
        yield [
            'requestData' => [
                'cardViewID' => 'notAInt',
                'sensorData' => [
                    "readingType" => "temperature",
                    "highReading" =>  25,
                    "lowReading" => 20,
                    "constRecord" =>  false
                ]
            ],
            'errorMessage' => 'cardViewID must be an int you have provided "notAInt"'
        ];

        yield [
            'requestData' => [
                'cardViewID' => null,
                'sensorData' => [
                    "readingType" => "temperature",
                    "highReading" =>  25,
                    "lowReading" => 20,
                    "constRecord" =>  false
                ]
            ],
            'errorMessage' => 'cardViewID cannot be null'
        ];
    }

    public function sendNoneExistentCardViewID($cardViewID): void
    {
        while (true) {
            $randomCardViewID = random_int(1, 10000);
            $cardViewObject = $this->entityManager->getRepository(CardView::class)->findOneBy(['cardViewID' => $randomCardViewID]);
            if (!$cardViewObject instanceof CardView) {
                break;
            }
        }

        $sensorData = [
            'sensorData' => [
                "readingType" => "temperature",
                "highReading" =>  25,
                "lowReading" => 20,
                "constRecord" =>  false
            ]
        ];
        $requestData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_CARD_FORM_URL, $cardViewID),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $requestData
        );

        $responseContent = $this->client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals(APIErrorMessages::FAILED_TO_PREPARE_DATA, $responseData['errors'][0]);
    }

    /**
     * @dataProvider userCannotEditOtherUsersCardsDataProvider
     */
    public function testInvalidUserCannotUpdateCardView(string $username, string $password, $usersCardToAlter): void
    {
        $userToken = $this->setUserToken(true, $username, $password);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $usersCardToAlter]);
        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findBy(['userID' => $user->getUserID()])[0];

        $sensorData = [
            'sensorData' => [
                [
                    "readingType" => "temperature",
                    "highReading" =>  25,
                    "lowReading" => 20,
                    "constRecord" =>  false
                ]
            ]
        ];
        $requestData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_CARD_FORM_URL, $cardViewObject->getCardViewID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
            $requestData
        );
        $responseContent = $this->client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        self::assertEquals(APIErrorMessages::ACCESS_DENIED, $responseData['errors'][0]);
        self::assertEquals('You Are Not Authorised To Be Here', $responseData['title']);
        self::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider sendingWrongCardDataRequestDataProvider
     */
    public function testSendingWrongCardDataRequest(
        bool $wrongColour,
        bool $wrongIcon,
        bool $wrongState,
        string $errorMessage,
    ): void {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findBy(['userID' => $user->getUserID()])[0];

        $cardColourRepository = $this->entityManager->getRepository(CardColour::class);
        $iconRepository = $this->entityManager->getRepository(Icons::class);
        $cardStateRepository = $this->entityManager->getRepository(Cardstate::class);

        if ($wrongColour === true) {
            while (true) {
                $randomColour = random_int(1, 10000);
                $cardColour = $cardColourRepository->findOneBy(['colourID' => $randomColour]);
                if (!$cardColour instanceof CardColour) {
                    $cardColour = $randomColour;
                    break;
                }
            }
        } else {
            $cardColour = $cardColourRepository->findAll()[0]->getColourID();
        }

        if ($wrongIcon === true) {
            while (true) {
                $randomIcon = random_int(1, 10000);
                $cardIcon = $iconRepository->findOneBy(['iconID' => $randomIcon]);
                if (!$cardIcon instanceof CardColour) {
                    $cardIcon = $randomIcon;
                    break;
                }
            }
        } else {
            $cardIcon = $iconRepository->findAll()[0]->getIconID();
        }

        if ($wrongState === true) {
            while (true) {
                $randomState = random_int(1, 10000);
                $cardState = $cardStateRepository->findOneBy(['cardStateID' => $randomState]);
                if (!$cardState instanceof Cardstate) {
                    $cardState = $randomState;
                    break;
                }
            }
        } else {
            $cardState = $cardStateRepository->findAll()[0]->getCardstateID();
        }

        $sensorData = [
            'cardColour' => $cardColour,
            'cardIcon' => $cardIcon,
            'cardViewState' => $cardState,
            'sensorData' => [
                [
                    "readingType" => "temperature",
                    "highReading" =>  25,
                    "lowReading" => 20,
                    "constRecord" =>  false
                ]
            ]
        ];

        $requestData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_CARD_FORM_URL, $cardViewObject->getCardViewID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $requestData
        );
        $responseContent = $this->client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        self::assertEquals($errorMessage, $responseData['errors'][0]);
        self::assertEquals('Bad Request No Data Returned', $responseData['title']);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function sendingWrongCardDataRequestDataProvider(): Generator
    {
        yield [
            'wrongColour' => true,
            'wrongIcon' => false,
            'wrongState' => false,
            'errorMessage' => 'Icons colour not found'
        ];

        yield [
            'wrongColour' => false,
            'wrongIcon' => true,
            'wrongState' => false,
            'errorMessage' => 'Icons icon not found'
        ];

        yield [
            'wrongColour' => false,
            'wrongIcon' => false,
            'wrongState' => true,
            'errorMessage' => 'Icons state not found'
        ];
    }

    /**
     * @dataProvider sendingNullCardViewDataProvider
     */
    public function testSendingNullCardViewData(
        bool $nullCardColour,
        bool $nullCardIcon,
        bool $nullCardState,
        string $errorMessage,
    ): void {
        $cardColourRepository = $this->entityManager->getRepository(CardColour::class);
        $iconRepository = $this->entityManager->getRepository(Icons::class);
        $cardStateRepository = $this->entityManager->getRepository(Cardstate::class);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findBy(['userID' => $user->getUserID()])[0];

        if ($nullCardColour === true) {
            $cardColour = null;
        } else {
            $cardColour = $cardColourRepository->findAll()[0]->getColourID();
        }

        if ($nullCardIcon === true) {
            $cardIcon = null;
        } else {
            $cardIcon = $iconRepository->findAll()[0]->getIconID();
        }

        if ($nullCardState === true) {
            $cardState = null;
        } else {
            $cardState = $cardStateRepository->findAll()[0]->getCardstateID();
        }

        $sensorData = [
            'cardColour' => $cardColour,
            'cardIcon' => $cardIcon,
            'cardViewState' => $cardState,
            'sensorData' => [
                [
                    "readingType" => "temperature",
                    "highReading" =>  25,
                    "lowReading" => 20,
                    "constRecord" =>  false
                ]
            ]
        ];

        $requestData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_CARD_FORM_URL, $cardViewObject->getCardViewID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $requestData
        );
        $responseContent = $this->client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        self::assertEquals($responseData['errors'][0], $errorMessage);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function sendingNullCardViewDataProvider(): Generator
    {
        yield [
            'nullCardColour' => true,
            'nullCardIcon' => false,
            'nullCardState' => false,
            'errorMessage' => 'Icons colour cannot be null'
        ];

        yield [
            'nullCardColour' => false,
            'nullCardIcon' => true,
            'nullCardState' => false,
            'errorMessage' => 'Icon cannot be null'
        ];

        yield [
            'nullCardColour' => false,
            'nullCardIcon' => false,
            'nullCardState' => true,
            'errorMessage' => 'Icons state cannot be null'
        ];
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }
}