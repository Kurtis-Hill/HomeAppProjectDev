<?php


namespace App\Services;


use App\Entity\Core\Devices;
use App\Entity\Core\Room;
use App\Entity\Core\Sensornames;
use App\HomeAppCore\HomeAppRoomAbstract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class NavbarService extends HomeAppRoomAbstract
{
    private $usersRooms;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        parent::__construct($em, $security);

        $this->usersRooms = $this->setUsersRooms();
    }

    private function setUsersRooms()
    {
        $roomRepository = $this->em->getRepository(Room::class);

        return $roomRepository->getRoomsForUser($this->groupNameid);
    }

    public function getUsersRooms()
    {
        return $this->usersRooms;
    }


    public function getAllSensorsByRoomForUser()
    {
        $sensorByRoom = $this->em->getRepository(Sensornames::class)->getAllSensorsForUser($this->usersRooms, $this->groupNameid);

        return $sensorByRoom;
    }

    public function getUsersDevices(): array
    {
        $devices = $this->em->getRepository(Devices::class)->returnAllUsersDevices($this->groupNameid);

        return $devices;
    }

}

