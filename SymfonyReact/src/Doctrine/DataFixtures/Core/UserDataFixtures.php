<?php

namespace App\Doctrine\DataFixtures\Core;

use App\Authentication\Entity\GroupNameMapping;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserDataFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURE_ORDER = 1;

    public const ADMIN_USER = 'admin-user@gmail.com';

    public const SECOND_ADMIN_USER_ISOLATED = 'admin-regular-test-email@testing.com';

    public const ADMIN_PASSWORD = 'admin1234';

    public const REGULAR_USER = 'regular-user';

    public const REGULAR_PASSWORD = 'user1234';

    public const ADMIN_GROUP = 'admin-group';

    public const REGULAR_GROUP = 'regular-group';

    public const USER_GROUP = 'user-group';

    public const SECOND_REGULAR_USER_ISOLATED = 'regular-user-admin-group@gmail.com';

    public const USER_ACCOUNTS = [
        self::ADMIN_GROUP,
        self::USER_GROUP,
        self::REGULAR_GROUP,
    ];

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getOrder(): int
    {
        return self::FIXTURE_ORDER;
    }

    public function load(ObjectManager $manager): void
    {
        // Admin User
        $adminGroupName = new GroupNames();

        $adminGroupName->setGroupName(self::ADMIN_GROUP);
        $adminGroupName->setCreatedAt();

        $adminUser = new User();

        $adminUser->setEmail(self::ADMIN_USER);
        $adminUser->setFirstName('admin');
        $adminUser->setLastName('test');
        $adminUser->setPassword($this->passwordEncoder->encodePassword($adminUser, self::ADMIN_PASSWORD));
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setCreatedAt();
        $adminUser->setGroupNameID($adminGroupName);

        $firstAminGroupName = new GroupNameMapping();

        $firstAminGroupName->setGroupNameID($adminGroupName);
        $firstAminGroupName->setUserID($adminUser);

        $manager->persist($firstAminGroupName);
        $manager->persist($adminGroupName);
        $manager->persist($adminUser);


        //Normal User
        $userGroupName = new GroupNames();

        $userGroupName->setGroupName(self::USER_GROUP);
        $userGroupName->setCreatedAt();

        $regularUser = new User();

        $regularUser->setEmail(self::REGULAR_USER);
        $regularUser->setFirstName('user');
        $regularUser->setLastName('test');
        $regularUser->setPassword($this->passwordEncoder->encodePassword($regularUser, self::REGULAR_PASSWORD));
        $regularUser->setRoles(['ROLE_USER']);
        $regularUser->setCreatedAt();
        $regularUser->setGroupNameID($userGroupName);

        $firstRegularGroupMapping = new GroupNameMapping();

        $firstRegularGroupMapping->setGroupNameID($userGroupName);
        $firstRegularGroupMapping->setUserID($regularUser);

        $manager->persist($firstRegularGroupMapping);
        $manager->persist($userGroupName);
        $manager->persist($regularUser);


        // Joining the two users by group mapping
        $adminUserInAdminGroup = new GroupNameMapping();

        $adminUserInAdminGroup->setGroupNameID($userGroupName);
        $adminUserInAdminGroup->setUserID($adminUser);

        $manager->persist($adminUserInAdminGroup);

        $regularUserAdminGroup = new GroupNameMapping();

        $regularUserAdminGroup->setGroupNameID($adminGroupName);
        $regularUserAdminGroup->setUserID($regularUser);


        //Just Admin Groups
        $adminUserGroupName = new GroupNames();

        $adminUserGroupName->setGroupName('second-admin-user-group');
        $adminUserGroupName->setCreatedAt();

        $adminUserInAdminGroup = new User();

        $adminUserInAdminGroup->setEmail(self::SECOND_ADMIN_USER_ISOLATED);
        $adminUserInAdminGroup->setFirstName('second-admin-user');
        $adminUserInAdminGroup->setLastName('test');
        $adminUserInAdminGroup->setPassword($this->passwordEncoder->encodePassword($adminUserInAdminGroup, self::ADMIN_PASSWORD));
        $adminUserInAdminGroup->setRoles(['ROLE_ADMIN']);
        $adminUserInAdminGroup->setCreatedAt();
        $adminUserInAdminGroup->setGroupNameID($adminUserGroupName);

        $secondAdminGroupMapping = new GroupNameMapping();

        $secondAdminGroupMapping->setGroupNameID($adminUserGroupName);
        $secondAdminGroupMapping->setUserID($adminUserInAdminGroup);

        $adminAdminGroupMapping = new GroupNameMapping();

        $adminAdminGroupMapping->setGroupNameID($adminGroupName);
        $adminAdminGroupMapping->setUserID($adminUserInAdminGroup);

        $manager->persist($adminAdminGroupMapping);
        $manager->persist($secondAdminGroupMapping);
        $manager->persist($adminUserGroupName);
        $manager->persist($adminUserInAdminGroup);

        //Just Regular Groups
        $secondRegularUserGroupName = new GroupNames();

        $secondRegularUserGroupName->setGroupName(self::SECOND_REGULAR_USER_ISOLATED);
        $secondRegularUserGroupName->setCreatedAt();

        $secondRegularUser = new User();

        $secondRegularUser->setEmail(self::SECOND_REGULAR_USER_ISOLATED);
        $secondRegularUser->setFirstName('second-regular-user');
        $secondRegularUser->setLastName('test');
        $secondRegularUser->setPassword($this->passwordEncoder->encodePassword($secondRegularUser, self::REGULAR_PASSWORD));
        $secondRegularUser->setRoles(['ROLE_USER']);
        $secondRegularUser->setCreatedAt();
        $secondRegularUser->setGroupNameID($adminUserGroupName);

        $secondRegularGroupMapping = new GroupNameMapping();

        $secondRegularGroupMapping->setGroupNameID($secondRegularUserGroupName);
        $secondRegularGroupMapping->setUserID($secondRegularUser);

        $regularRegularGroupMapping = new GroupNameMapping();

        $regularRegularGroupMapping->setGroupNameID($userGroupName);
        $regularRegularGroupMapping->setUserID($secondRegularUser);

        $manager->persist($regularRegularGroupMapping);
        $manager->persist($secondRegularGroupMapping);
        $manager->persist($secondRegularUser);
        $manager->persist($secondRegularUserGroupName);

        $this->addReference(self::ADMIN_USER, $adminUser);
        $this->addReference(self::REGULAR_USER, $regularUser);
        $this->addReference(self::SECOND_ADMIN_USER_ISOLATED, $adminUserInAdminGroup);

        $this->addReference(self::ADMIN_GROUP, $adminGroupName);
        $this->addReference(self::REGULAR_GROUP, $userGroupName);
        $this->addReference(self::SECOND_REGULAR_USER_ISOLATED, $secondRegularUserGroupName);
        $manager->flush();
    }
}
