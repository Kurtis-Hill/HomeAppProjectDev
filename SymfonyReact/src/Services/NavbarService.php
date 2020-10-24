<?php


namespace App\Services;


use App\Entity\Core\Devices;
use App\Entity\Core\GroupMapping;
use App\Entity\Core\Room;
use App\Entity\Core\Sensornames;
use App\HomeAppCore\HomeAppRoomAbstract;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Security\Core\Security;

class NavbarService extends HomeAppRoomAbstract
{
    private $usersRooms = [];

    private $groupNames = [];

    private $devices = [];

    private $errors = [];

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        parent::__construct($em, $security);

        try {
            $this->usersRooms = $this->setUsersRooms();
            $this->groupNames = $this->setUsersGroupNames();
            $this->devices = $this->setUsersDevices();
        } catch (\PDOException $e) {
            $this->errors['errors'] = $e->getMessage();
        }  catch (\Exception $e) {
            $this->errors['errors'] = $e->getMessage();
        }
    }


    private function setUsersRooms(): ?array
    {
        $userRooms = $this->em->getRepository(Room::class)->getRoomsForUser($this->groupNameIDs);

        return $userRooms;
    }

    private function setUsersGroupNames(): ?array
    {
        $groupNames = $this->em->getRepository(GroupMapping::class)->getGroupNamesAndIds($this->userID);

        return $groupNames;
    }

    private function setUsersDevices(): ?array
    {
        $devices = $this->em->getRepository(Devices::class)->returnAllUsersDevices($this->groupNameIDs);

        return $devices;
    }


    public function getUserDevices(): ?array
    {
        return $this->devices;
    }

    public function getUsersRooms(): ?array
    {
        return $this->usersRooms;
    }

    public function getUsersGroupNames(): ?array
    {
        return $this->groupNames;
    }

    public function getErrors(): ?array
    {
        $this->errors['userErrors'] = $this->getErrors();

        return $this->errors;
    }
}

