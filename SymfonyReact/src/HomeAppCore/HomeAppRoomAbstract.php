<?php


namespace App\HomeAppCore;


use App\Entity\Core\GroupMapping;
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
    /**
     * @var Security
     */
    protected $user;

    /**
     * @var EntityManager|EntityManagerInterface
     */
    protected $em;

    /**
     * @var User
     */
    protected $userID;


    /**
     * @var GroupMapping
     */
    protected $groupNameids;


    /**
     * HomeAppRoomAbstract constructor.
     * @param EntityManagerInterface $em
     * @param Security $security
     */
    public function __construct(EntityManagerInterface $em, Security $security)
    {
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
        $this->userID = $this->user->getUser()->getUserid();
        $this->groupNameids = $this->groupNameids = $this->em->getRepository(GroupMapping::class)->getGroupsForUser($this->userID);

        if ($this->groupNameids === null || $this->userID === null) {
            throw new \Exception("The User Variables Cannot be set Please try again");
        }

    }

    public function getGroupNameID()
    {
        return $this->groupNameids;
    }

    public function getUserID()
    {
        return $this->groupNameids;
    }

}