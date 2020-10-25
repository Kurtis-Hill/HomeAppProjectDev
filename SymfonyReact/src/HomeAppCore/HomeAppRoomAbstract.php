<?php


namespace App\HomeAppCore;


use App\Entity\Core\GroupMapping;
use App\Entity\Core\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Core\Security;

abstract class HomeAppRoomAbstract
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
     * @var array
     */
    protected $userID;

    /**
     * @var array
     */
    protected $roles;

    /**
     * @var array
     */
    protected $groupNameIDs = [];

    protected $userErrors = [];

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
            $this->userErrors[] = $e->getMessage();
        }
    }

    /**
     * @throws \Exception
     */
    private function setUserVariables()
    {
        $this->userID = $this->user->getUser()->getUserid();
        $this->groupNameIDs = $this->groupNameIDs = $this->em->getRepository(GroupMapping::class)->getGroupsForUser($this->userID);
        $this->roles = $this->user->getUser()->getRoles();

        if (!$this->groupNameIDs || !$this->userID || empty($this->roles)) {
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

    public function getUserRoles()
    {
        return $this->roles;
    }

    public function getUserErrors()
    {
        return $this->errors;
    }
}