<?php

namespace App\DataFixtures\Core;

use App\Entity\Core\GroupNames;
use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserDataFixtures extends Fixture implements OrderedFixtureInterface
{
    public const ADMIN_USER = 'admin-user@gmail.com';

    public const ADMIN_PASSWORD = 'admin1234';

    public const REGULAR_USER = 'regular-user';

    public const ADMIN_GROUP = 'admin-group';

    public const REGULAR_GROUP = 'regular-group';


    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getOrder(): int
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        // Admin User
        $adminGroupName = new GroupNames();

        $adminGroupName->setGroupName(self::ADMIN_GROUP);
        $adminGroupName->setTime();

        $adminUser = new User();

        $adminUser->setEmail(self::ADMIN_USER);
        $adminUser->setFirstName('admin');
        $adminUser->setLastName('test');
        $adminUser->setPassword($this->passwordEncoder->encodePassword($adminUser, self::ADMIN_PASSWORD));
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setTime();
        $adminUser->setGroupNameID($adminGroupName);

        $groupMapping = new GroupnNameMapping();

        $groupMapping->setGroupNameID($adminGroupName);
        $groupMapping->setUserID($adminUser);

        $manager->persist($groupMapping);
        $manager->persist($adminGroupName);
        $manager->persist($adminUser);


        //Normal User
        $userGroupName = new GroupNames();

        $userGroupName->setGroupName('user-group');
        $userGroupName->setTime();

        $regularUser = new User();

        $regularUser->setEmail('user-test-email@testing.com');
        $regularUser->setFirstName('user');
        $regularUser->setLastName('test');
        $regularUser->setPassword($this->passwordEncoder->encodePassword($regularUser, 'user1234'));
        $regularUser->setRoles(['ROLE_USER']);
        $regularUser->setTime();
        $regularUser->setGroupNameID($userGroupName);

        $groupMapping = new GroupnNameMapping();

        $groupMapping->setGroupNameID($userGroupName);
        $groupMapping->setUserID($regularUser);

        $manager->persist($groupMapping);
        $manager->persist($userGroupName);
        $manager->persist($regularUser);


        $manager->flush();

        $this->addReference(self::ADMIN_USER, $adminUser);
        $this->addReference(self::REGULAR_USER, $regularUser);

        $this->addReference(self::ADMIN_GROUP, $adminGroupName);
        $this->addReference(self::REGULAR_GROUP, $userGroupName);
    }
}
