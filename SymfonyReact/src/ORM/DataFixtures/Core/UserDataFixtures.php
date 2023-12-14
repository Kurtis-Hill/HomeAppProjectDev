<?php

namespace App\ORM\DataFixtures\Core;

use App\Authentication\Entity\GroupMapping;
use App\User\Entity\Group;
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

    public const REGULAR_USER_EMAIL_THREE = 'regular-user-3-regular-user-one-group@gmail.com';

    public const REGULAR_PASSWORD = 'user1234';

    public const REGULAR_GROUP_ONE = 'regular-group-one';

    public const REGULAR_GROUP_TWO = 'regular-group-two';

    public const REGULAR_GROUP_THREE = 'regular-group-three';

    public const ADMIN_USER_GROUP_TWO = 'second-admin-user-group';

    public const UNIQUE_USER_EMAIL_NOT_TO_BE_USED = 'unique-user@gmail.com';

    public const UNIQUE_GROUP_NAME_NOT_TO_BE_USED = 'uniquegroupname';

    /** groups of users with mappings to each other */
    public const GROUPS_SECOND_REGULAR_USER_IS_ADDED_TO = [
        self::REGULAR_GROUP_TWO,
        Group::HOME_APP_GROUP_NAME,
        self::ADMIN_GROUP_ONE,
    ];

    public const ALL_GROUPS = [
        Group::HOME_APP_GROUP_NAME,
        self::ADMIN_GROUP_ONE,
        self::ADMIN_GROUP_TWO,
        self::REGULAR_GROUP_ONE,
        self::REGULAR_GROUP_TWO,
        self::REGULAR_GROUP_THREE,
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

        $adminUserOne = new User();

        $adminUserOne->setEmail(self::ADMIN_USER_EMAIL_ONE);
        $adminUserOne->setFirstName('adminONE');
        $adminUserOne->setLastName('test');
        $adminUserOne->setPassword($this->passwordEncoder->hashPassword($adminUserOne, self::ADMIN_PASSWORD));
        $adminUserOne->setRoles(['ROLE_ADMIN']);
        $adminUserOne->setCreatedAt();

        $manager->persist($adminUserOne);
        $manager->flush();

        $adminGroupOne = new Group();

        $adminGroupOne->setGroupName(self::ADMIN_GROUP_ONE);
        $adminGroupOne->setCreatedAt();
        $adminGroupOne->setCreatedBy($adminUserOne);
        //Default home app group name
        $homeAppGroupName = new Group();
        $homeAppGroupName->setGroupName(Group::HOME_APP_GROUP_NAME);
        $homeAppGroupName->setCreatedAt();
        $homeAppGroupName->setCreatedBy($adminUserOne);
        $manager->persist($adminGroupOne);

        $manager->persist($homeAppGroupName);

        $adminUserTwo = new User();

        $adminUserTwo->setEmail(self::ADMIN_USER_EMAIL_TWO);
        $adminUserTwo->setFirstName('adminTWO');
        $adminUserTwo->setLastName('test');
        $adminUserTwo->setPassword($this->passwordEncoder->hashPassword($adminUserTwo, self::ADMIN_PASSWORD));
        $adminUserTwo->setRoles(['ROLE_ADMIN']);
        $adminUserTwo->setCreatedAt();

        $manager->persist($adminUserTwo);
        $manager->flush();

        $regularUserOne = new User();

        $regularUserOne->setEmail(self::REGULAR_USER_EMAIL_ONE);
        $regularUserOne->setFirstName('user');
        $regularUserOne->setLastName('test');
        $regularUserOne->setPassword($this->passwordEncoder->hashPassword($regularUserOne, self::REGULAR_PASSWORD));
        $regularUserOne->setRoles(['ROLE_USER']);
        $regularUserOne->setCreatedAt();
        $manager->persist($regularUserOne);
        $manager->flush();

        $regularUserOneHomeAppGroupMapping = new GroupMapping();
        $regularUserOneHomeAppGroupMapping->setUser($regularUserOne);
        $regularUserOneHomeAppGroupMapping->setGroup($homeAppGroupName);

        //Second Admin User
        $adminTwoGroupName = new Group();

        $adminTwoGroupName->setGroupName(self::ADMIN_USER_GROUP_TWO);
        $adminTwoGroupName->setCreatedAt();
        $adminTwoGroupName->setCreatedBy($adminUserOne);
        $manager->persist($adminTwoGroupName);

        //Regular User Not part of Admin Group
        $userGroupOne = new Group();
        $userGroupOne->setGroupName(self::REGULAR_GROUP_ONE);
        $userGroupOne->setCreatedAt();
        $userGroupOne->setCreatedBy($adminUserOne);

        $manager->persist($userGroupOne);


        $regularUserTwo = new User();
        $regularUserTwo->setEmail(self::REGULAR_USER_EMAIL_TWO);
        $regularUserTwo->setFirstName('second-regular-user');
        $regularUserTwo->setLastName('test');
        $regularUserTwo->setPassword($this->passwordEncoder->hashPassword($regularUserTwo, self::REGULAR_PASSWORD));
        $regularUserTwo->setRoles(['ROLE_USER']);
        $regularUserTwo->setCreatedAt();

        $manager->persist($regularUserTwo);
        $manager->flush();

        //Regular User in Admin Group
        $regularUserGroupTwo = new Group();
        $regularUserGroupTwo->setGroupName(self::REGULAR_GROUP_TWO);
        $regularUserGroupTwo->setCreatedAt();
        $regularUserGroupTwo->setCreatedBy($adminUserOne);

//        $regularUserTwoHomeGroupNameMappingEntry = new GroupMapping();
//        $regularUserTwoHomeGroupNameMappingEntry->setUser($regularUserTwo);
//        $regularUserTwoHomeGroupNameMappingEntry->setGroup($homeAppGroupName);
//
//        $manager->persist($regularUserTwoHomeGroupNameMappingEntry);

        $regularUserTwoAdminGroupOneGroupNameMapping = new GroupMapping();

        $regularUserTwoAdminGroupOneGroupNameMapping->setUser($regularUserTwo);
        $regularUserTwoAdminGroupOneGroupNameMapping->setGroup($adminGroupOne);

        $regularUserTwoHomeGroupNameMappingEntry = new GroupMapping();
        $regularUserTwoHomeGroupNameMappingEntry->setUser($regularUserTwo);
        $regularUserTwoHomeGroupNameMappingEntry->setGroup($homeAppGroupName);

        $manager->persist($regularUserTwoHomeGroupNameMappingEntry);

        $manager->persist($regularUserTwoAdminGroupOneGroupNameMapping);
        $manager->persist($regularUserGroupTwo);

        $regularUserThreeGroup = new Group();
        $regularUserThreeGroup->setGroupName(self::REGULAR_GROUP_THREE);
        $regularUserThreeGroup->setCreatedAt();
        $regularUserThreeGroup->setCreatedBy($adminUserOne);
        $manager->persist($regularUserThreeGroup);

        $regularUserThree = new User();
        $regularUserThree->setEmail(self::REGULAR_USER_EMAIL_THREE);
        $regularUserThree->setFirstName('third-regular-user');
        $regularUserThree->setLastName('test');
        $regularUserThree->setPassword($this->passwordEncoder->hashPassword($regularUserThree, self::REGULAR_PASSWORD));
        $regularUserThree->setRoles(['ROLE_USER']);
        $regularUserThree->setCreatedAt();

        $manager->persist($regularUserThree);
        $manager->flush();

        $regularUserThreeRegularUserOneGroupNameMapping = new GroupMapping();
        $regularUserThreeRegularUserOneGroupNameMapping->setUser($regularUserThree);
        $regularUserThreeRegularUserOneGroupNameMapping->setGroup($userGroupOne);

        $regularUserThreeHomeGroupNameMapping = new GroupMapping();
        $regularUserThreeHomeGroupNameMapping->setUser($regularUserThree);
        $regularUserThreeHomeGroupNameMapping->setGroup($homeAppGroupName);
        $manager->persist($regularUserThreeHomeGroupNameMapping);

        $regularUserThreeRegularUserTwoGroupNameMapping = new GroupMapping();
        $regularUserThreeRegularUserTwoGroupNameMapping->setUser($regularUserThree);
        $regularUserThreeRegularUserTwoGroupNameMapping->setGroup($regularUserGroupTwo);
        $manager->persist($regularUserThreeRegularUserTwoGroupNameMapping);


        $this->addReference(self::ADMIN_USER_EMAIL_ONE, $adminUserOne);
        $this->addReference(self::ADMIN_USER_EMAIL_TWO, $adminUserTwo);
        $this->addReference(self::REGULAR_USER_EMAIL_ONE, $regularUserOne);
        $this->addReference(self::REGULAR_USER_EMAIL_TWO, $regularUserTwo);
        $this->addReference(self::REGULAR_USER_EMAIL_THREE, $regularUserThree);

        $this->addReference(self::ADMIN_GROUP_ONE, $adminGroupOne);
        $this->addReference(self::ADMIN_GROUP_TWO, $adminTwoGroupName);
        $this->addReference(self::REGULAR_GROUP_ONE, $userGroupOne);
        $this->addReference(self::REGULAR_GROUP_TWO, $regularUserGroupTwo);
        $manager->flush();
    }
}
