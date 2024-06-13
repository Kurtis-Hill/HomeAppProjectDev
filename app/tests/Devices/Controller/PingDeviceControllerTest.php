<?php

namespace App\Tests\Devices\Controller;

use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Device\Devices;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Repository\Device\ORM\DeviceRepository;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PingDeviceControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const PING_DEVICE_URL = '/HomeApp/api/user/user-devices/%d/ping';

    private ?string $adminToken = null;

    private ?EntityManagerInterface $entityManager;

    private DeviceRepository $deviceRepository;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);

        $this->adminToken = $this->setUserToken($this->client);
    }

    protected function tearDown() : void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_pinging_device_doesnt_exist(): void
    {
        while (true) {
            $randomID = random_int(1, 9999);
            $device = $this->deviceRepository->find($randomID);
            if ($device === null) {
                break;
            }
        }

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PING_DEVICE_URL, $randomID),
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
            ]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_pinging_device_no_access_to(): void
    {
        /** @var \App\Entity\User\User $regularUserTwo */
        $regularUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);

        $regularUserToken = $this->setUserToken($this->client, $regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);
        /** @var Group[] $groupsNotApartOf */
        $groupsNotApartOf = $this->entityManager->getRepository(Group::class)->findGroupsUserIsNotApartOf(
            $regularUserTwo,
            $regularUserTwo->getAssociatedGroupIDs()
        );

        /** @var \App\Entity\Device\Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsNotApartOf]);
        $device = $devices[array_rand($devices)];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PING_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $regularUserToken,
            ]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
