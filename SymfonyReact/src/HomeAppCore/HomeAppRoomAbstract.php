<?php


namespace App\HomeAppCore;


use App\Entity\Core\Groupname;
use App\Entity\Core\Room;
use App\Entity\Core\Sensortype;
use App\Entity\Core\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class HomeAppRoomAbstract
{
    protected $user;

    protected $currentRoom;

    protected $userID;

    protected $currentSensorType;

    protected $allSensorTypes;

    protected $em;

    protected $groupNameid;




    /**
     * HomeAppRoomAbstract constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $request = new Request();

        $this->em = $em;

        $this->user = $security;
        try {
            $this->setUserVariables();
        } catch (\Exception $e) {
            $e->getMessage();
        }

    }

    private function setUserVariables()
    {
        $this->groupNameid = $this->user->getUser()->getGroupNameId();
        $this->userID = $this->user->getUser()->getUserid();

        if ($this->groupNameid === null || $this->userID === null) {
            throw new Exception("The User Variables Cannot be set Please try again");
        }

    }



}