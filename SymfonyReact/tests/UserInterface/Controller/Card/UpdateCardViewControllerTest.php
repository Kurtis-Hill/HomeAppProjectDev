<?php

namespace App\Tests\UserInterface\Controller\Card;

use App\Common\API\APIErrorMessages;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Controller\SensorControllers\UpdateSensorBoundaryReadingsController;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\User;
use App\UserInterface\Entity\Card\CardColour;
use App\UserInterface\Entity\Card\CardState;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Entity\Icons;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateCardViewControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const UPDATE_CARD_FORM_URL =  '/HomeApp/api/user/card/%d/update';

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

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;

        parent::tearDown();
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

//        dd($this->client->getResponse()->getContent());
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
            /** @var CardView $cardViewObject */
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
        $userToken = $this->setUserToken($this->client, $username, $password);

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $usersCardToAlter]);
        /** @var CardView $cardViewObject */
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

    public function userCannotEditOtherUsersCardsDataProvider(): Generator
    {
        yield [
            'username' => UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            'password' => UserDataFixtures::REGULAR_PASSWORD,
            'alter' => UserDataFixtures::ADMIN_USER_EMAIL_ONE,
        ];

        yield [
            'username' => UserDataFixtures::REGULAR_USER_EMAIL_TWO,
            'password' => UserDataFixtures::REGULAR_PASSWORD,
            'alter' => UserDataFixtures::REGULAR_USER_EMAIL_ONE,
        ];

        yield [
            'username' => UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            'password' => UserDataFixtures::REGULAR_PASSWORD,
            'alter' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
        ];
//        yield [
//            'username' => UserDataFixtures::ADMIN_USER,
//            'password' => UserDataFixtures::ADMIN_PASSWORD,
//            'alter' => UserDataFixtures::REGULAR_USER,
//        ];
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
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        /** @var CardView $cardViewObject */
        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findBy(['userID' => $user->getUserID()])[0];

        $cardColourRepository = $this->entityManager->getRepository(CardColour::class);
        $iconRepository = $this->entityManager->getRepository(Icons::class);
        $cardStateRepository = $this->entityManager->getRepository(CardState::class);

        if ($wrongColour === true) {
            while (true) {
                $randomColour = random_int(1, 10000);
                /** @var CardColour $cardColour */
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
                /** @var Icons $cardIcon */
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
                /** @var CardState $cardState */
                $cardState = $cardStateRepository->find($randomState);
                if (!$cardState instanceof CardState) {
                    $cardState = $randomState;
                    break;
                }
            }
        } else {
            /** @var CardState[] $cardStates */
            $cardStates = $cardStateRepository->findAll();
            $cardState = $cardStates[0]->getStateID();
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
            'errorMessage' => 'Colour not found'
        ];

        yield [
            'wrongColour' => false,
            'wrongIcon' => true,
            'wrongState' => false,
            'errorMessage' => 'Icon not found'
        ];

        yield [
            'wrongColour' => false,
            'wrongIcon' => false,
            'wrongState' => true,
            'errorMessage' => 'Card State state not found'
        ];
    }

    /**
     * @dataProvider sendingNullCardViewDataProvider
     */
    public function testSendingNullCardViewData(
        bool $nullCardColour,
        bool $nullCardIcon,
        bool $nullCardState,
    ): void {
        $cardColourRepository = $this->entityManager->getRepository(CardColour::class);
        $iconRepository = $this->entityManager->getRepository(Icons::class);
        $cardStateRepository = $this->entityManager->getRepository(CardState::class);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findBy(['userID' => $user->getUserID()])[0];

        if ($nullCardColour === true) {
            $cardColour = null;
        } else {
            $cardColourObject = $cardColourRepository->findAll()[0];
            $cardColour = $cardColourObject->getColourID();
        }

        if ($nullCardIcon === true) {
            $cardIcon = null;
        } else {
            $cardIconObject = $iconRepository->findAll()[0];
            $cardIcon = $cardIconObject->getIconID();
        }

        if ($nullCardState === true) {
            $cardState = null;
        } else {
            /** @var CardState[] $cardStateObject */
            $cardStateObject = $cardStateRepository->findAll();
            $cardState = $cardStateObject[0]->getStateID();
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

        self::assertEquals(UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL, $responseData['title']);
        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertEquals($cardViewObject->getCardViewID(), $responseData['payload']['cardViewID']);

        if ($cardColour !== null) {
            self::assertEquals($cardColourObject->getColourID(), $responseData['payload']['cardColour']['colourID']);
            self::assertEquals($cardColourObject->getColour(), $responseData['payload']['cardColour']['colour']);
            self::assertEquals($cardColourObject->getShade(), $responseData['payload']['cardColour']['shade']);
        }
        if ($cardIcon !== null) {
            self::assertEquals($cardIconObject->getIconID(), $responseData['payload']['cardIcon']['iconID']);
            self::assertEquals($cardIconObject->getIconName(), $responseData['payload']['cardIcon']['iconName']);
            self::assertEquals($cardIconObject->getDescription(), $responseData['payload']['cardIcon']['description']);
        }
        //@TODO find out why card state is lazy loading and the rest arnt
//        if ($cardState !== null) {
//
//            self::assertEquals($cardStateObject->getCardstateID(), $responseData['payload']['cardViewState']['cardStateID']);
//            self::assertEquals($cardStateObject->getCardstate(), $responseData['payload']['cardViewState']['cardState']);
//        }
    }

    public function sendingNullCardViewDataProvider(): Generator
    {
        yield [
            'nullCardColour' => true,
            'nullCardIcon' => false,
            'nullCardState' => false,
        ];

        yield [
            'nullCardColour' => false,
            'nullCardIcon' => true,
            'nullCardState' => false,
        ];

        yield [
            'nullCardColour' => false,
            'nullCardIcon' => false,
            'nullCardState' => true,
        ];
    }
}
