<?php


namespace App\Services;


use App\Entity\Core\Devices;
use App\Entity\Core\GroupMapping;
use App\Entity\Core\Room;
use App\Entity\Core\Sensornames;
use App\HomeAppCore\HomeAppRoomAbstract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class NavbarService extends HomeAppRoomAbstract
{
    private $usersRooms = [];

    private $groupNames = [];

    private $devices = [];

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        parent::__construct($em, $security);

        try {
            $this->usersRooms = $this->setUsersRooms();

            $this->groupNames = $this->setUsersGroupNames();

            $this->devices = $this->setUsersDevices();
        }
        catch (\Exception $e) {
            dd($e->getMessage());
        }

    }

    private function setUsersRooms()
    {
        $roomRepository = $this->em->getRepository(Room::class);

        return $roomRepository->getRoomsForUser($this->groupNameIDs);
    }

    private function setUsersGroupNames()
    {
        $groupNames = $this->em->getRepository(GroupMapping::class)->getGroupNamesAndIds($this->userID);

        return $groupNames;
    }

    private function setUsersDevices(): array
    {
        $devices = $this->em->getRepository(Devices::class)->returnAllUsersDevices($this->groupNameIDs);

        return $devices;
    }


    public function getUserDevices()
    {
        return $this->devices;
    }

    public function getUsersRooms()
    {
        return $this->usersRooms;
    }


    public function getUsersGroupNames()
    {
        return $this->groupNames;
    }


}

