<?php

namespace App\Doctrine\DataFixtures\Core;

use App\Authentication\Entity\GroupNameMapping;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDataFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURE_ORDER = 1;

    public const ADMIN_USER_EMAIL = 'admin-user@gmail.com';

    public const SECOND_ADMIN_USER_EMAIL = 'admin-user-two@gmail.com';

    public const ADMIN_PASSWORD = 'admin1234';

    public const REGULAR_USER_EMAIL = 'regular-user-admin-group@gmail.com';

    public const REGULAR_PASSWORD = 'user1234';

    public const ADMIN_GROUP = 'admin-group';

    public const REGULAR_GROUP = 'regular-group';

    public const USER_GROUP = 'user-group';

    public const SECOND_ADMIN_USER_GROUP = 'second-admin-user-group';

    public const SECOND_REGULAR_USER_ADMIN_GROUP = 'regular-user-admin-group@gmail.com';

    public const UNIQUE_USER_EMAIL_NOT_TO_BE_USED = 'unique-user@gmail.com';

    public const UNIQUE_GROUP_NAME_NOT_TO_BE_USED = 'uniquegroupname';

    public const USER_GROUPS = [
        self::ADMIN_GROUP,
        self::REGULAR_GROUP,
    ];

    public const ALL_GROUPS = [
        self::ADMIN_GROUP,
        self::USER_GROUP,
        self::REGULAR_GROUP,
    ];

    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getOrder(): int
    {
        return self::FIXTURE_ORDER;
    }

    public function load(ObjectManager $manager): void
    {
        // Admin User One
        $adminGroupName = new GroupNames();

        $adminGroupName->setGroupName(self::ADMIN_GROUP);
        $adminGroupName->setCreatedAt();

        $adminUser = new User();

        $adminUser->setEmail(self::ADMIN_USER_EMAIL);
        $adminUser->setFirstName('admin');
        $adminUser->setLastName('test');
        $adminUser->setPassword($this->passwordEncoder->hashPassword($adminUser, self::ADMIN_PASSWORD));
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setCreatedAt();
        $adminUser->setGroupNameID($adminGroupName);

//        $firstAminGroupName = new GroupNameMapping();
//
//        $firstAminGroupName->setGroupName($adminGroupName);
//        $firstAminGroupName->setUser($adminUser);

//        $manager->persist($firstAminGroupName);
        $manager->persist($adminGroupName);
        $manager->persist($adminUser);




//        $firstRegularGroupMapping = new GroupNameMapping();

//        $firstRegularGroupMapping->setGroupName($userGroupName);
//        $firstRegularGroupMapping->setUser($regularUser);

//        $manager->persist($firstRegularGroupMapping);



        // Joining the two users by group mapping
//        $adminUserInAdminGroup = new GroupNameMapping();
//
//        $adminUserInAdminGroup->setGroupName($userGroupName);
//        $adminUserInAdminGroup->setUser($adminUser);
//
//        $manager->persist($adminUserInAdminGroup);

//        $regularUserAdminGroup = new GroupNameMapping();
//
//        $regularUserAdminGroup->setGroupName($adminGroupName);
//        $regularUserAdminGroup->setUser($regularUser);


        //Second Admin User
        $adminUserGroupName = new GroupNames();

        $adminUserGroupName->setGroupName(self::SECOND_ADMIN_USER_GROUP);
        $adminUserGroupName->setCreatedAt();

        $adminUserInAdminGroup = new User();

        $adminUserInAdminGroup->setEmail(self::SECOND_ADMIN_USER_EMAIL);
        $adminUserInAdminGroup->setFirstName('second-admin-user');
        $adminUserInAdminGroup->setLastName('test');
        $adminUserInAdminGroup->setPassword($this->passwordEncoder->hashPassword($adminUserInAdminGroup, self::ADMIN_PASSWORD));
        $adminUserInAdminGroup->setRoles(['ROLE_ADMIN']);
        $adminUserInAdminGroup->setCreatedAt();
        $adminUserInAdminGroup->setGroupNameID($adminUserGroupName);

        $manager->persist($adminUserGroupName);
        $manager->persist($adminUserInAdminGroup);
//        $secondAdminGroupMapping = new GroupNameMapping();
//
//        $secondAdminGroupMapping->setGroupName($adminUserGroupName);
//        $secondAdminGroupMapping->setUser($adminUserInAdminGroup);

//        $adminAdminGroupMapping = new GroupNameMapping();

//        $adminAdminGroupMapping->setGroupName($adminGroupName);
//        $adminAdminGroupMapping->setUser($adminUserInAdminGroup);

//        $manager->persist($adminAdminGroupMapping);
//        $manager->persist($secondAdminGroupMapping);


        //Normal User Not part of Admin Group
        $userGroupName = new GroupNames();

        $userGroupName->setGroupName(self::USER_GROUP);
        $userGroupName->setCreatedAt();

        $this->addReference(self::USER_GROUP, $userGroupName);
        $regularUser = new User();

        $regularUser->setEmail(self::REGULAR_USER_EMAIL);
        $regularUser->setFirstName('user');
        $regularUser->setLastName('test');
        $regularUser->setPassword($this->passwordEncoder->hashPassword($regularUser, self::REGULAR_PASSWORD));
        $regularUser->setRoles(['ROLE_USER']);
        $regularUser->setCreatedAt();
        $regularUser->setGroupNameID($userGroupName);
        $manager->persist($userGroupName);
        $manager->persist($regularUser);

        //Regular User in Admin Group
        $secondRegularUserGroupName = new GroupNames();

        $secondRegularUserGroupName->setGroupName(self::SECOND_REGULAR_USER_ADMIN_GROUP);
        $secondRegularUserGroupName->setCreatedAt();

        $secondRegularUser = new User();

        $secondRegularUser->setEmail(self::SECOND_REGULAR_USER_ADMIN_GROUP);
        $secondRegularUser->setFirstName('second-regular-user');
        $secondRegularUser->setLastName('test');
        $secondRegularUser->setPassword($this->passwordEncoder->hashPassword($secondRegularUser, self::REGULAR_PASSWORD));
        $secondRegularUser->setRoles(['ROLE_USER']);
        $secondRegularUser->setCreatedAt();
        $secondRegularUser->setGroupNameID($secondRegularUserGroupName);

//        $secondRegularGroupMapping = new GroupNameMapping();
//
//        $secondRegularGroupMapping->setGroupName($secondRegularUserGroupName);
//        $secondRegularGroupMapping->setUser($secondRegularUser);

        $regularRegularGroupMapping = new GroupNameMapping();

        $regularRegularGroupMapping->setGroupName($adminGroupName);
        $regularRegularGroupMapping->setUser($secondRegularUser);

        $manager->persist($regularRegularGroupMapping);
//        $manager->persist($secondRegularGroupMapping);
        $manager->persist($secondRegularUser);
        $manager->persist($secondRegularUserGroupName);

        $this->addReference(self::ADMIN_USER_EMAIL, $adminUser);
        $this->addReference(self::REGULAR_USER_EMAIL, $regularUser);
        $this->addReference(self::SECOND_ADMIN_USER_EMAIL, $adminUserInAdminGroup);

        $this->addReference(self::ADMIN_GROUP, $adminGroupName);
        $this->addReference(self::REGULAR_GROUP, $userGroupName);
        $this->addReference(self::SECOND_REGULAR_USER_ADMIN_GROUP, $secondRegularUserGroupName);
        $manager->flush();
    }
}
