<?php


namespace App\Services;


use App\Entity\Core\Room;
use App\Entity\Devices\Devices;
use App\HomeAppSensorCore\ESPDeviceSensor\AbstractHomeAppUserSensorServiceCore;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;


class UserInterfaceService extends AbstractHomeAppUserSensorServiceCore
{
    private array $userRooms;

    private array $userDevices;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        parent::__construct($em, $security);
        $this->userRooms = $this->em->getRepository(Room::class)->getRoomsForUser($this->getGroupNameIDs());
        $this->userDevices = $this->em->getRepository(Devices::class)->getAllUsersDevices($this->getGroupNameIDs());

        //dd($this->userRooms, $this->userDevices);
    }

    public function getNavBarData(): array
    {
//        dd($this->userDevices, $this->userRooms, $this->getGroupNameDetails());
        return  [
            'rooms' => $this->userRooms,
            'devices' => $this->userDevices,
            'groupNames' => $this->getGroupNameDetails()
        ];
    }

    public function getAppUserDataForLocalStorage()
    {
        return [
            'userID' => $this->getUserID(),
            'roles' => $this->getUser()->getRoles(),
            'groups' => $this->getGroupNameDetails(),
        ];
    }

    public function getErrors()
    {
        return $this->getFatalErrors();
    }
}
