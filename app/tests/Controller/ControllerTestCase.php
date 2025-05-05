<?php

namespace App\Tests\Controller;

use App\Controller\Authentication\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Device\Devices;
use App\Entity\Sensor\AbstractSensorType;
use App\Entity\Sensor\Sensor;
use App\Entity\User\Group;
use App\Entity\User\Room;
use App\Entity\User\User;
use App\Entity\UserInterface\Card\CardState;
use App\Entity\UserInterface\Card\CardView;
use App\Entity\UserInterface\Card\Colour;
use App\Entity\UserInterface\Icons;
use App\Services\Request\RequestTypeEnum;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

abstract class ControllerTestCase extends WebTestCase
{
    public const MULTI_STATE_DEFAULT_MESSAGE = 'Error(s) have occurred.';
    public const VALIDATION_ERROR_MESSAGE = 'Validation error(s) have occurred.';

    protected KernelBrowser $client;

    protected ?EntityManagerInterface $entityManager;

    protected User $adminOne;

    protected User $adminTwo;

    protected User $regularUserOne;

    protected User $regularUserTwo;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();

        $this->adminOne = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        $this->adminTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]);
        $this->regularUserOne = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $this->regularUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
    }

    protected function tearDown(): void
    {
        $this->entityManager->clear();
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    protected static function assertValidationErrorMessage(string $title)
    {
        self::assertEquals('Validation errors occurred', $title);
    }

    /**
     * @throws JsonException
     */
    protected function authenticateAdminOne(): void
    {
        $email = $this->adminOne->getEmail();
        $password = UserDataFixtures::ADMIN_PASSWORD;

        $this->setUserToken($email, $password);
    }

    protected function authenticateAdminTwo(): void
    {
        $email = $this->adminTwo->getEmail();
        $password = UserDataFixtures::ADMIN_PASSWORD;

        $this->setUserToken($email, $password);
    }

    protected function authenticateRegularUserOne(): void
    {
        $email = $this->regularUserOne->getEmail();
        $password = UserDataFixtures::REGULAR_PASSWORD;

        $this->setUserToken($email, $password);
    }

    protected function authenticateRegularUserTwo(): void
    {
        $email = $this->regularUserTwo->getEmail();
        $password = UserDataFixtures::REGULAR_PASSWORD;

        $this->setUserToken($email, $password);
    }

    private function setUserToken(string $email, string $password): void
    {
        $this->client->request(
            Request::METHOD_POST,
            SecurityController::API_USER_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"'. $email .'","password":"'. $password .'"}'
        );

        self::assertResponseIsSuccessful();

        $requestResponse = $this->client->getResponse();
        try {
            $responseData = json_decode(
                $requestResponse->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            throw new JsonException('Failed to (json)decode user/device login token request');
        }

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $responseData['token']));
    }

    protected static function assertDeviceIsSameAsExpected(Devices $device, array $response): void
    {
        self::assertEquals($device->getDeviceID(), $response['deviceID']);
        self::assertEquals($device->getDeviceName(), $response['deviceName']);

        if (!empty($response['group'])) {
            self::assertGroupIsSamAsExpected($device->getGroupObject(), $response['group']);
        }

        if (!empty($response['room'])) {
            self::assertRoomIsSamAsExpected($device->getRoomObject(), $response['room']);
        }

        if (!empty($response['ipAddress'])) {
            self::assertEquals($device->getIpAddress(), $response['ipAddress']);
        }

        if (!empty($response['externalIpAddress'])) {
            self::assertEquals($device->getExternalIpAddress(), $response['externalIpAddress']);
        }

        if (!empty($response['roles'])) {
            self::assertEquals($device->getRoles(), $response['roles']);
        }

        if (!empty($response['createdById']))    {
            self::assertEquals($device->getCreatedById(), $response['createdById']);
        }
    }

    protected static function assertGroupIsSamAsExpected(Group $group, array $response): void
    {
        self::assertEquals($group->getGroupID(), $response['groupID']);
        self::assertEquals($group->getGroupName(), $response['groupName']);
    }

    protected static function assertRoomIsSamAsExpected(Room $room, array $response): void
    {
        self::assertEquals($room->getRoomID(), $response['roomID']);
        self::assertEquals($room->getRoom(), $response['roomName']);
    }

    protected static function assertUserIsSameAsExpected(User $user, array $response): void
    {
        self::assertEquals($user->getUserID(), $response['userID']);
        self::assertEquals($user->getFirstName(), $response['firstName']);
        self::assertEquals($user->getLastName(), $response['lastName']);
        self::assertEquals($user->getEmail(), $response['email']);

        if (!empty($response['group'])) {
            self::assertGroupIsSamAsExpected($user->getGroup(), $response['group']);
        }
        if (!empty($response['createdAt'])) {
            self::assertEquals($user->getCreatedAt()->format(DateTimeInterface::ATOM), $response['createdAt']);
        }
        self::assertEquals($user->getProfilePic(), $response['profilePicture'] ?? null);
        if (!empty($response['roles'])) {
            self::assertEquals($user->getRoles(), $response['roles']);
        }
    }

    protected static function assertSensorIsSameAsExpected(Sensor $sensor, array $response): void
    {
        self::assertEquals($sensor->getSensorID(), $response['sensorID']);
        self::assertEquals($sensor->getSensorName(), $response['sensorName']);
        self::assertEquals($sensor->getPinNumber(), $response['pinNumber']);
        self::assertEquals($sensor->getReadingInterval(), $response['readingInterval']);

        if (!empty($response['createdBy'])) {
            self::assertUserIsSameAsExpected($sensor->getCreatedBy(), $response['createdBy']);
        }

        if (!empty($response['device'])) {
            self::assertDeviceIsSameAsExpected($sensor->getDevice(), $response['device']);
        }

        if (!empty($response['sensorType'])) {
            self::assertSensorTypeIsSameAsExpected($sensor->getSensorTypeObject(), $response['sensorType']);
        }

//        if (!empty($response['cardView'])) {
//            self::assertCardViewIsSameAsExpected($sensor->get(), $response['cardView']);
//        }
    }

    protected static function assertSensorTypeIsSameAsExpected(AbstractSensorType $sensorType, array $response): void
    {
        self::assertEquals($sensorType->getSensorTypeID(), $response['sensorTypeID']);
        self::assertEquals($sensorType::getSensorTypeName(), $response['sensorTypeName']);
        self::assertEquals($sensorType->getDescription(), $response['sensorTypeDescription']);
    }

    protected static function assertCardViewIsSameAsExpected(CardView $cardView, array $response): void
    {
        self::assertEquals($cardView->getCardViewID(), $response['cardViewID']);
        self::assertIconIsSameAsExpected($cardView->getCardIconID(), $response['cardIcon']);
        self::assertColourIsSameAsExpected($cardView->getCardColourID(), $response['cardColour']);
        self:self::assertStateIsSameAsExpected($cardView->getCardStateID(), $response['cardState']);;
    }

    protected static function assertIconIsSameAsExpected(Icons $icon, array $response): void
    {
        self::assertEquals($icon->getIconID(), $response['iconID']);
        self::assertEquals($icon->getIconName(), $response['iconName']);
        self::assertEquals($icon->getDescription(), $response['description']);
    }

    protected static function assertColourIsSameAsExpected(Colour $colour, array $response): void
    {
        self::assertEquals($colour->getColourID(), $response['colourID']);
        self::assertEquals($colour->getColour(), $response['colour']);
        self::assertEquals($colour->getShade(), $response['shade']);
    }

    protected static function assertStateIsSameAsExpected(CardState $state, array $response): void
    {
        self::assertEquals($state->getStateID(), $response['cardStateID']);
        self::assertEquals($state->getState(), $response['cardState']);
    }
}
