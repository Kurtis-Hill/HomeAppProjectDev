<?php

namespace App\Tests\User\Controller\GroupNameMappingControllers;

use App\Authentication\Entity\GroupNameMapping;
use App\Authentication\Repository\ORM\GroupNameMappingRepository;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Tests\Traits\TestLoginTrait;
use App\User\Controller\GroupNameMappingControllers\GetGroupNameMappingsController;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupNameRepositoryInterface;
use App\User\Repository\ORM\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetGroupNameMappingsControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_GROUP_MAPPING_URL = '/HomeApp/api/user/group-mapping/all';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private string $userToken;

    private User $user;

    private User $regularUserTwo;

    private GroupNameRepositoryInterface $groupNameRepository;

    private UserRepositoryInterface $userRepository;

    private GroupNameMappingRepository $groupNameMappingRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        $this->regularUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        $this->userToken = $this->setUserToken($this->client);
        $this->groupNameRepository = $this->entityManager->getRepository(GroupNames::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::GET_GROUP_MAPPING_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): Generator
    {
        yield [Request::METHOD_POST];
        yield [Request::METHOD_PATCH];
        yield [Request::METHOD_PUT];
        yield [Request::METHOD_DELETE];
    }

    public function test_admins_can_see_all_group_name_mappings(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_GROUP_MAPPING_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];

        $allGroupNameMappings = $this->groupNameMappingRepository->findAll();

        self::assertCount(count($allGroupNameMappings), $payload);
    }

    public function test_regular_users_can_see_all_own_group_name_mappings(): void
    {
        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_GET,
            self::GET_GROUP_MAPPING_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];

        $allGroupNameMappingsForUser = $this->groupNameMappingRepository->findBy([
            'user' => $this->regularUserTwo,
        ]);

        self::assertCount(count($allGroupNameMappingsForUser), $payload);
    }

    public function test_full_response_data_is_correct(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_GROUP_MAPPING_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);

        $allGroupNameMappings = $this->groupNameMappingRepository->findAll();

        self::assertCount(count($allGroupNameMappings), $payload);

        foreach ($payload as $groupNameMapping) {
            self::assertArrayHasKey('groupNameMappingID', $groupNameMapping);
            self::assertArrayHasKey('groupName', $groupNameMapping);
            self::assertArrayHasKey('user', $groupNameMapping);
        }
    }
}