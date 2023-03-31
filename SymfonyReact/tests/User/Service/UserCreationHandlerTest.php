<?php

namespace App\Tests\User\Service;

use App\Authentication\Entity\GroupNameMapping;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Exceptions\GroupNameExceptions\GroupNameValidationException;
use App\User\Exceptions\UserExceptions\UserCreationValidationErrorsException;
use App\User\Repository\ORM\GroupNameRepository;
use App\User\Repository\ORM\UserRepository;
use App\User\Services\User\UserCreationHandler;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserCreationHandlerTest extends KernelTestCase
{
    private const PROFILE_PICTURE_TEST = '/tests/User/assets/profile-pic.jpg';

    private UserCreationHandler $sut;

    private ?EntityManagerInterface $entityManager = null;

    private GroupNameRepository $groupRepository;

    private UserRepository $userRepository;

    private string $projectDir;

    private string $uploadProfilePictureDir;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = self::getContainer();
        $this->sut = $container->get(UserCreationHandler::class);
        $this->entityManager = $container->get('doctrine')->getManager();
        $this->groupRepository = $this->entityManager->getRepository(GroupNames::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->projectDir = $kernel->getProjectDir();
        $this->uploadProfilePictureDir = $_ENV['USER_PROFILE_DIRECTORY'];
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_handle_new_user_creation_duplicate_groupname(): void
    {
        $this->expectException(GroupNameValidationException::class);

        $this->sut->handleNewUserCreation(
            'John',
            'Doe',
            'test@gmail.com',
            UserDataFixtures::ADMIN_GROUP_ONE,
            'password',
        );
    }

    public function test_adding_duplicate_user_email(): void
    {
        $this->expectException(UserCreationValidationErrorsException::class);

        $this->sut->handleNewUserCreation(
            'first',
            'last',
            UserDataFixtures::ADMIN_USER_EMAIL_ONE,
            'test-group',
            'nhlkhhhgnbggfgg',
        );
    }

    public function test_uploading_profile_picture_throws_file_exception(): void
    {
        $uploadFile = $this->createMock(UploadedFile::class);
        $uploadFile->method('move')->willThrowException(new FileException());

        $this->sut->handleNewUserCreation(
            'John',
            'Doe',
            UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
            UserDataFixtures::UNIQUE_GROUP_NAME_NOT_TO_BE_USED,
            'password',
            $uploadFile,
        );

        $userCheck = $this->userRepository->findOneBy(['email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED]);

        self::assertInstanceOf(User::class, $userCheck);
        self::assertEquals('John', $userCheck->getFirstName());
        self::assertEquals('Doe', $userCheck->getLastName());
        self::assertEquals(UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED, $userCheck->getEmail());
        self::assertEquals(UserDataFixtures::UNIQUE_GROUP_NAME_NOT_TO_BE_USED, $userCheck->getGroupNameID()->getGroupName());
        self::assertEquals(User::DEFAULT_PROFILE_PICTURE, $userCheck->getProfilePic());
    }

    public function test_profile_picture_file_gets_uploaded_to_correct_location(): void
    {
        copy(
            $this->projectDir . self::PROFILE_PICTURE_TEST,
            $this->projectDir . self::PROFILE_PICTURE_TEST . '.bak',
        );
        $uploadFile = new UploadedFile(
            $this->projectDir . self::PROFILE_PICTURE_TEST,
            'profile-pic.jpg',
            null,
            null,
            true,
        );

        $user = $this->sut->handleNewUserCreation(
            'John',
            'Doe',
            UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
            UserDataFixtures::UNIQUE_GROUP_NAME_NOT_TO_BE_USED,
            'password',
            $uploadFile,
        );

        rename(
            $this->projectDir . self::PROFILE_PICTURE_TEST . '.bak',
            $this->projectDir . self::PROFILE_PICTURE_TEST,
        );

        self::assertFileEquals(
            $this->projectDir . self::PROFILE_PICTURE_TEST,
            $this->projectDir . $this->uploadProfilePictureDir . $user->getProfilePic(),
        );

        unlink($this->projectDir . $this->uploadProfilePictureDir . $user->getProfilePic());
    }

    /**
     * @dataProvider invalidUserDataProvider
     */
    public function test_create_user_invalid_user_data(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $groupName,
        array $errors = []
    ): void {
        $this->expectException(UserCreationValidationErrorsException::class);

        $this->sut->handleNewUserCreation(
            $firstName,
            $lastName,
            $email,
            $groupName,
            $password,
        );
        self::assertEquals($errors, $this->sut->getErrors());
    }


    public function invalidUserDataProvider(): Generator
    {
        yield [
                'firstName' => '',
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'password' => 'password',
                'groupName' => UserDataFixtures::UNIQUE_GROUP_NAME_NOT_TO_BE_USED,
                'errors' => [
                    'First name cannot be empty',
                ],
        ];

        yield [
                'firstName' => 'John',
                'lastName' => '',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'password' => 'password',
                'groupName' => UserDataFixtures::UNIQUE_GROUP_NAME_NOT_TO_BE_USED,
                'errors' => [
                    'Last name cannot be empty',
                ],
        ];

        yield [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => '',
                'password' => 'password',
                'groupName' => UserDataFixtures::UNIQUE_GROUP_NAME_NOT_TO_BE_USED,
                'errors' => [
                    'Email cannot be empty',
                ],
        ];

        yield [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'test',
                'password' => 'password',
                'groupName' => UserDataFixtures::UNIQUE_GROUP_NAME_NOT_TO_BE_USED,
                'errors' => [
                    'Email is not valid',
                ],
        ];

        yield [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'password' => '',
                'groupName' => UserDataFixtures::UNIQUE_GROUP_NAME_NOT_TO_BE_USED,
                'errors' => [
                    'Password cannot be empty',
                ],
        ];
    }

    /**
     * @dataProvider invalidGroupNameDataProvider
     */
    public function test_create_user_invalid_group_name_data(string $groupName, array $errors): void
    {
        $this->expectException(GroupNameValidationException::class);

        $this->sut->handleNewUserCreation(
            'John',
            'Doe',
            UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
            $groupName,
            'password',
        );
    }

    public function invalidGroupNameDataProvider(): Generator
    {
        yield [
            'groupName' => '',
            'errors' => [
                'Group name cannot be empty',
            ],
        ];

        yield [
            'groupName' => 'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest',
            'errors' => [
                'Group name is not valid',
            ],
        ];
    }

    public function test_create_user_with_null_profile_picture(): void
    {
        $user = $this->sut->handleNewUserCreation(
            'John',
            'Doe',
            UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
            UserDataFixtures::UNIQUE_GROUP_NAME_NOT_TO_BE_USED,
            'nhlkhhhgnbggfgg',
        );

        $userSaved = $this->userRepository->findOneBy(['email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED]);

        self::assertEquals($userSaved, $user);
        self::assertEquals(User::DEFAULT_PROFILE_PICTURE, $user->getProfilePic());
    }

    public function test_create_user_correct_data(): void
    {
        copy(
            $this->projectDir . self::PROFILE_PICTURE_TEST,
            $this->projectDir . self::PROFILE_PICTURE_TEST . '.bak',
        );
        $uploadFile = new UploadedFile(
            $this->projectDir . self::PROFILE_PICTURE_TEST,
            'profile-pic.jpg',
            null,
            null,
            true,
        );

        $user = $this->sut->handleNewUserCreation(
            'John',
            'Doe',
            UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
            UserDataFixtures::UNIQUE_GROUP_NAME_NOT_TO_BE_USED,
            'hghnkjhgfhgfghgf',
            $uploadFile,
        );

        rename(
            $this->projectDir . self::PROFILE_PICTURE_TEST . '.bak',
            $this->projectDir . self::PROFILE_PICTURE_TEST,
        );

        unlink($this->projectDir . $this->uploadProfilePictureDir . $user->getProfilePic());
        $userSaved = $this->userRepository->findOneBy(['email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED]);

        self::assertEquals($userSaved, $user);
    }

    public function test_group_name_is_created(): void
    {
        $user = $this->sut->handleNewUserCreation(
            'John',
            'Doe',
            UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
            UserDataFixtures::UNIQUE_GROUP_NAME_NOT_TO_BE_USED,
            'hghnkjhgfhgfghgf',
        );

        $group = $this->groupRepository->findOneBy(['groupName' => UserDataFixtures::UNIQUE_GROUP_NAME_NOT_TO_BE_USED]);

        self::assertEquals($group, $user->getGroupNameID());
    }

    public function test_adding_user_with_admin_roles(): void
    {
        $this->sut->handleNewUserCreation(
            'John',
            'Doe',
            UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
            UserDataFixtures::UNIQUE_GROUP_NAME_NOT_TO_BE_USED,
            'hghnkjhgfhgfghgf',
            null,
            ['ROLE_ADMIN'],
        );

        $userSaved = $this->userRepository->findOneBy(['email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED]);

        self::assertSame($userSaved->getRoles()[0], 'ROLE_ADMIN');
    }

    //  Disabled on open system for security reasons
//    public function test_every_new_user_none_admin_gets_added_to_home_app_group(): void
//    {
//        $this->sut->handleNewUserCreation(
//            'John',
//            'Doe',
//            UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
//            UserDataFixtures::UNIQUE_GROUP_NAME_NOT_TO_BE_USED,
//            'hghnkjhgfhgfghgf',
//            null,
//            ['ROLE_USER'],
//        );
//
//        /** @var User $userSaved */
//        $userSaved = $this->userRepository->findOneBy(['email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED]);
//
//        $groupMappingRepository = $this->entityManager->getRepository(GroupNames::class);
//        /** @var GroupNames $homeAppGroup */
//        $homeAppGroup = $groupMappingRepository->findOneBy(['groupName' => GroupNames::HOME_APP_GROUP_NAME]);
//
//        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);
//
//        /** @var GroupNameMapping $homeGroupMappingEntry */
//        $homeGroupMappingEntry = $groupNameMappingRepository->findOneBy([
//            'groupName' => $homeAppGroup->getGroupNameID(),
//            'user' => $userSaved->getUserID(),
//        ]);
//
//        self::assertNotNull($homeGroupMappingEntry);
//    }

    public function test_every_new_admin_user_gets_added_to_home_app_group(): void
    {
        $this->sut->handleNewUserCreation(
            'John',
            'Doe',
            UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
            UserDataFixtures::UNIQUE_GROUP_NAME_NOT_TO_BE_USED,
            'hghnkjhgfhgfghgf',
            null,
            [User::ROLE_ADMIN],
        );

        /** @var User $userSaved */
        $userSaved = $this->userRepository->findOneBy(['email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED]);

        $groupMappingRepository = $this->entityManager->getRepository(GroupNames::class);
        $homeAppGroup = $groupMappingRepository->findOneBy(['groupName' => GroupNames::HOME_APP_GROUP_NAME]);

        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);

        $homeGroupMappingEntry = $groupNameMappingRepository->findOneBy([
            'groupName' => $homeAppGroup->getGroupNameID(),
            'user' => $userSaved->getUserID(),
        ]);

        self::assertNull($homeGroupMappingEntry);
    }
}
