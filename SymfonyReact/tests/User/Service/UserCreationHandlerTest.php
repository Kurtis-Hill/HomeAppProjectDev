<?php

namespace App\Tests\User\Service;

use App\Doctrine\DataFixtures\Core\UserDataFixtures;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupNameRepository;
use App\User\Repository\ORM\UserRepository;
use App\User\Services\User\UserCreationHandler;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserCreationHandlerTest extends KernelTestCase
{
    private UserCreationHandler $sut;

    private ?EntityManagerInterface $entityManager = null;

    private GroupNameRepository $groupRepository;

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->sut = $container->get(UserCreationHandler::class);
        $this->entityManager = $container->get('doctrine')->getManager();
        $this->groupRepository = $this->entityManager->getRepository(GroupNames::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_handle_new_user_creation_duplicate_groupname(): void
    {
        $this->expectException(UniqueConstraintViolationException::class);

        $this->sut->handleNewUserCreation(
            'John',
            'Doe',
            'test@gmail.com',
            'password',
            UserDataFixtures::USER_GROUP,
        );

//        $userCheck = $this->userRepository->findOneBy(['email' => 'test@gmail.com')

//        dd($handleUserCreationResult);

//        $fixtureCheck = $this->groupRepository->findOneBy(['groupName' => UserDataFixtures::USER_GROUP]);

//        if ($fixtureCheck === null) {
//            self::fail('Fixture not found');
//        }


//        self::assertStringContainsString('Group name already exists', $handleUserCreationResult[0]);
    }

//    public function test_uploading_profile_picture_throws_file_exception()
//    {
//
//    }
//
//    public function test_profile_pitcure_file_gets_uploaded_to_correct_location()
//    {
//
//    }
//
//
//    /**
//     * @dataProvider invalidUserDataProvider
//     */
//    public function test_create_user_invalid_user_data()
//    {
//
//    }
//
//    public function invalidUserDataProvider(): array
//    {
//        return [
//            'empty_first_name' => [
//                'firstName' => '',
//                'lastName' => 'Doe',
//                'email' => ''
//            ]
//        ];
//    }
//
//    public function test_create_user_with_null_profile_picture()
//    {
//
//    }
//
//    public function test_create_user_correct_data()
//    {
//    }
//
//    public function test_group_name_is_created(): void
//    {
//
//    }
//
//    public function test_adding_user_with_admin_roles(): void
//    {
//
//    }
//
//    public function test_user_and_group_are_added_to_group_name_mapping(): void
//    {
//
//    }

}
