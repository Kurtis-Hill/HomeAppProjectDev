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
    private array $usersRooms = [];

    private array $groupNames = [];

    private array $devices = [];

    private array $errors = [];

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
           error_log($e->getMessage());
        }

    }

    /**
     * @return array
     */
    private function setUsersRooms(): array
    {

        $userRooms = $this->em->getRepository(Room::class)->getRoomsForUser($this->getGroupNameIDs());

        if (empty($userRooms)) {
            $this->errors[] = 'User rooms are empty';
        }

        return $userRooms;
    }

    /**
     * @return array
     */
    private function setUsersGroupNames(): array
    {
        $groupNames = $this->em->getRepository(GroupnNameMapping::class)->getUserGroupNamesAndIDs($this->getUserID());
dd($groupNames);
        if (empty($groupNames)) {
            $this->errors[] = 'User group names are empty';
        }

        return $groupNames;
    }

    /**
     * @return array
     */
    private function setUsersDevices(): array
    {
        $devices = $this->em->getRepository(Devices::class)->getAllUsersDevices($this->getGroupNameIDs());

        if (empty($devices)) {
            $this->errors[] = 'User devices names are empty';
        }

        return $devices;
    }

    /**
     * @return array
     */
    public function getUserDevices(): array
    {
        return $this->devices;
    }

    /**
     * @return array
     */
    public function getUsersRooms(): array
    {
        return $this->usersRooms;
    }

    /**
     * @return array
     */
    public function getUsersGroupNames(): array
    {
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
