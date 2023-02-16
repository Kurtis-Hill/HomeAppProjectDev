<?php

namespace App\ORM\DataFixtures\Core;

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

    public const ADMIN_USER_EMAIL_ONE = 'admin-user@gmail.com';

    public const ADMIN_USER_EMAIL_TWO = 'admin-user-two@gmail.com';

    public const ADMIN_GROUP_ONE = 'admin-group-one';

    public const ADMIN_GROUP_TWO = 'admin-group-two';


    public const ADMIN_PASSWORD = 'admin1234';


    public const REGULAR_USER_EMAIL_ONE = 'regular-user@gmail.com';

    /** this user has group name mapping relating to the first admin users groups */
    public const REGULAR_USER_EMAIL_TWO = 'regular-user-admin-group@gmail.com';

    public const REGULAR_PASSWORD = 'user1234';

    public const REGULAR_GROUP_ONE = 'regular-group-one';

    public const REGULAR_GROUP_TWO = 'regular-group-two';


    public const ADMIN_USER_GROUP_TWO = 'second-admin-user-group';


    public const UNIQUE_USER_EMAIL_NOT_TO_BE_USED = 'unique-user@gmail.com';

    public const UNIQUE_GROUP_NAME_NOT_TO_BE_USED = 'uniquegroupname';

    /** groups of users with mappings to each other */
    public const GROUPS_SECOND_REGULAR_USER_IS_ADDED_TO = [
        self::ADMIN_GROUP_ONE,
        self::REGULAR_GROUP_TWO,
    ];

    public const ALL_GROUPS = [
        self::ADMIN_GROUP_ONE,
        self::ADMIN_GROUP_TWO,
        self::REGULAR_GROUP_ONE,
        self::REGULAR_GROUP_TWO,
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
        $adminGroupOne = new GroupNames();

        $adminGroupOne->setGroupName(self::ADMIN_GROUP_ONE);
        $adminGroupOne->setCreatedAt();

        $adminUserOne = new User();

        $adminUserOne->setEmail(self::ADMIN_USER_EMAIL_ONE);
        $adminUserOne->setFirstName('adminONE');
        $adminUserOne->setLastName('test');
        $adminUserOne->setPassword($this->passwordEncoder->hashPassword($adminUserOne, self::ADMIN_PASSWORD));
        $adminUserOne->setRoles(['ROLE_ADMIN']);
        $adminUserOne->setCreatedAt();
        $adminUserOne->setGroupNameID($adminGroupOne);

//        $firstAminGroupName = new GroupNameMapping();
//
//        $firstAminGroupName->setGroupName($adminGroupName);
//        $firstAminGroupName->setUser($adminUser);

//        $manager->persist($firstAminGroupName);
        $manager->persist($adminGroupOne);
        $manager->persist($adminUserOne);




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
        $adminTwoGroupName = new GroupNames();

        $adminTwoGroupName->setGroupName(self::ADMIN_USER_GROUP_TWO);
        $adminTwoGroupName->setCreatedAt();

        $adminUserTwo = new User();

        $adminUserTwo->setEmail(self::ADMIN_USER_EMAIL_TWO);
        $adminUserTwo->setFirstName('adminTWO');
        $adminUserTwo->setLastName('test');
        $adminUserTwo->setPassword($this->passwordEncoder->hashPassword($adminUserTwo, self::ADMIN_PASSWORD));
        $adminUserTwo->setRoles(['ROLE_ADMIN']);
        $adminUserTwo->setCreatedAt();
        $adminUserTwo->setGroupNameID($adminTwoGroupName);

        $manager->persist($adminTwoGroupName);
        $manager->persist($adminUserTwo);
//        $secondAdminGroupMapping = new GroupNameMapping();
//
//        $secondAdminGroupMapping->setGroupName($adminUserGroupName);
//        $secondAdminGroupMapping->setUser($adminUserInAdminGroup);

//        $adminAdminGroupMapping = new GroupNameMapping();

//        $adminAdminGroupMapping->setGroupName($adminGroupName);
//        $adminAdminGroupMapping->setUser($adminUserInAdminGroup);

//        $manager->persist($adminAdminGroupMapping);
//        $manager->persist($secondAdminGroupMapping);


        //Regular User Not part of Admin Group
        $userGroupOne = new GroupNames();

        $userGroupOne->setGroupName(self::REGULAR_GROUP_ONE);
        $userGroupOne->setCreatedAt();

        $regularUserOne = new User();

        $regularUserOne->setEmail(self::REGULAR_USER_EMAIL_ONE);
        $regularUserOne->setFirstName('user');
        $regularUserOne->setLastName('test');
        $regularUserOne->setPassword($this->passwordEncoder->hashPassword($regularUserOne, self::REGULAR_PASSWORD));
        $regularUserOne->setRoles(['ROLE_USER']);
        $regularUserOne->setCreatedAt();
        $regularUserOne->setGroupNameID($userGroupOne);
        $manager->persist($userGroupOne);
        $manager->persist($regularUserOne);



        //Regular User in Admin Group
        $regularUserGroupTwo = new GroupNames();

        $regularUserGroupTwo->setGroupName(self::REGULAR_GROUP_TWO);
        $regularUserGroupTwo->setCreatedAt();

        $regularUserTwo = new User();

        $regularUserTwo->setEmail(self::REGULAR_USER_EMAIL_TWO);
        $regularUserTwo->setFirstName('second-regular-user');
        $regularUserTwo->setLastName('test');
        $regularUserTwo->setPassword($this->passwordEncoder->hashPassword($regularUserTwo, self::REGULAR_PASSWORD));
        $regularUserTwo->setRoles(['ROLE_USER']);
        $regularUserTwo->setCreatedAt();
        $regularUserTwo->setGroupNameID($regularUserGroupTwo);

        $regularUserTwoAdminGroupOneGroupNameMapping = new GroupNameMapping();

        $regularUserTwoAdminGroupOneGroupNameMapping->setGroupName($adminGroupOne);
        $regularUserTwoAdminGroupOneGroupNameMapping->setUser($regularUserTwo);

        $manager->persist($regularUserTwoAdminGroupOneGroupNameMapping);
        $manager->persist($regularUserTwo);
        $manager->persist($regularUserGroupTwo);

        $this->addReference(self::ADMIN_USER_EMAIL_ONE, $adminUserOne);
        $this->addReference(self::ADMIN_USER_EMAIL_TWO, $adminUserTwo);
        $this->addReference(self::REGULAR_USER_EMAIL_ONE, $regularUserOne);
        $this->addReference(self::REGULAR_USER_EMAIL_TWO, $regularUserTwo);

//        $this->addReference();
        $this->addReference(self::ADMIN_GROUP_ONE, $adminGroupOne);
        $this->addReference(self::ADMIN_GROUP_TWO, $adminTwoGroupName);
        $this->addReference(self::REGULAR_GROUP_ONE, $userGroupOne);
        $this->addReference(self::REGULAR_GROUP_TWO, $regularUserGroupTwo);
        $manager->flush();
    }
}
