<?php


namespace App\HomeAppCore;


use App\Entity\Core\GroupMapping;
use App\Entity\Core\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Core\Security;

class HomeAppRoomAbstract
{
    /**
     * @var Security
     */
    private $user;

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
    protected $groupNameIDs = [];


    /**
     * HomeAppRoomAbstract constructor.
     * @param EntityManagerInterface $em
     * @param Security $security
     *
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

    /**
     * @throws \Exception
     */
    private function setUserVariables()
    {
        $this->userID = $this->user->getUser()->getUserid();
        $this->groupNameIDs = $this->groupNameIDs = $this->em->getRepository(GroupMapping::class)->getGroupsForUser($this->userID);

        if (!$this->groupNameIDs || !$this->userID) {
            throw new \Exception("The User Variables Cannot be set Please try again");
        }

    }

    public function getGroupNameID()
    {
        return $this->groupNameIDs;
    }

    public function getUserID()
    {
        return $this->userID;
    }

}