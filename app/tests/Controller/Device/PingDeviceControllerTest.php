<?php

namespace App\Tests\Controller\Device;

use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Device\Devices;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Repository\Device\ORM\DeviceRepository;
use App\Tests\Controller\ControllerTestCase;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PingDeviceControllerTest extends ControllerTestCase
{
    private const PING_DEVICE_URL = '/HomeApp/api/user/user-devices/%d/ping';

    private DeviceRepository $deviceRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
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

        $this->authenticateAdminOne();
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PING_DEVICE_URL, $randomID),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_pinging_device_no_access_to(): void
    {
        /** @var User $regularUserTwo */
        $regularUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);

        /** @var Group[] $groupsNotApartOf */
        $groupsNotApartOf = $this->entityManager->getRepository(Group::class)->findGroupsUserIsNotApartOf(
            $regularUserTwo,
            $regularUserTwo->getAssociatedGroupIDs()
        );

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsNotApartOf]);
        $device = $devices[array_rand($devices)];

        $this->authenticateRegularUserTwo();
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::PING_DEVICE_URL, $device->getDeviceID()),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
