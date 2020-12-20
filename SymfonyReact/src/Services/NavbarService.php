<?php


namespace App\Services;



use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\Room;

use App\Entity\Sensors\Devices;
use App\HomeAppCore\HomeAppCoreAbstract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

// class needs a slight refactor
class NavbarService extends HomeAppCoreAbstract
{
    private $usersRooms = [];

    private $groupNames = [];

    private $devices = [];

    private $errors = [];

    /**
     * NavbarService constructor.
     * @param EntityManagerInterface $em
     * @param Security $security
     */
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        parent::__construct($em, $security);

        try {
            $this->usersRooms = $this->setUsersRooms();
            $this->groupNames = $this->setUsersGroupNames();
            $this->devices = $this->setUsersDevices();
        }
        catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
        }

    }

    /**
     * @return array
     */
    private function setUsersRooms(): array
    {
        $roomRepository = $this->em->getRepository(Room::class);

        return $roomRepository->getRoomsForUser($this->groupNameIDs);
    }

    /**
     * @return array
     */
    private function setUsersGroupNames(): array
    {
        $groupNames = $this->em->getRepository(GroupnNameMapping::class)->getUserGroupNamesAndIds($this->userID);

        return $groupNames;
    }

    /**
     * @return array
     */
    private function setUsersDevices(): array
    {
        $devices = $this->em->getRepository(Devices::class)->getAllUsersDevices($this->groupNameIDs);

        return $devices;
    }

    /**
     * @return array
     */
    public function getUserDevices(): array
    {
        if (empty($this->devices)) {
            $this->devices['devicename'] = 'No devices';
        }
        return $this->devices;
    }

    /**
     * @return array
     */
    public function getUsersRooms(): array
    {
        if (empty($this->usersRooms)) {
            $this->usersRooms = ['room' => 'No user rooms', 'roomid' => 0];
        }
        return $this->usersRooms;
    }

    /**
     * @return array
     */
    public function getUsersGroupNames(): array
    {
        if (empty($this->groupNames)) {
            $this->groupNames = ['groupname' => 'No user groups', 'groupnameid' => 0];
        }
        return $this->groupNames;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

}
