<?php

namespace App\Tests\UserInterface\Controller\Card;

use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Authentication\Controller\SecurityController;
use App\Common\API\APIErrorMessages;
use App\Sensors\Controller\SensorControllers\UpdateSensorBoundaryReadingsController;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use App\Tests\Traits\TestLoginTrait;
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

class GetSingleCardViewController extends WebTestCase
{
    use TestLoginTrait;

    private const GET_CARD_FORM_URL =  '/HomeApp/api/user/card/%d/get';

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

    /**
     * @dataProvider getCardViewFormDataProvider
     */
    public function test_get_card_view_form_data(string $sensorType): void
    {
        /** @var SensorType $sensorType */
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findBy(['sensorTypeID' => $sensorType->getSensorTypeID()])[0];

        /** @var CardView $cardViewObject */
        $cardViewObject = $this->entityManager->getRepository(CardView::class)->findBy(['sensor' => $sensor->getSensorID()])[0];

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

        /** @var Icons[] $allIcons */
        $allIcons = $this->entityManager->getRepository(Icons::class)->findAll();
        /** @var CardColour[] $allCardColours */
        $allCardColours = $this->entityManager->getRepository(CardColour::class)->findAll();
        /** @var Cardstate[] $allCardState */
        $allCardState = $this->entityManager->getRepository(Cardstate::class)->findAll();

        self::assertEquals($cardViewObject->getCardViewID(), $responseData['cardViewID']);

        self::assertEquals($cardViewObject->getCardIconID()->getIconID(), $responseData['currentCardIcon']['iconID']);
        self::assertCount(count($allIcons), $responseData['cardUserSelectionOptions']['icons']);

        self::assertCount(count($allCardState), $responseData['cardUserSelectionOptions']['states']);

        self::assertEquals($cardViewObject->getCardColourID()->getColourID(), $responseData['currentCardColour']['colourID']);
        self::assertCount(count($allCardColours), $responseData['cardUserSelectionOptions']['colours']);


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
            /** @var CardView $cardView */
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

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider userCannotEditOtherUsersCardsDataProvider
     */
    public function test_user_cannot_edit_other_users_cards(string $username, string $password, $usersCardToAlter): void
    {
        $userToken = $this->setUserToken($this->client, $username, $password);

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $usersCardToAlter]);

        /** @var CardView $cardViewObject */
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
}
