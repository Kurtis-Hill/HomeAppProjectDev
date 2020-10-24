<?php


namespace App\HomeAppCore;


use App\Entity\Core\GroupMapping;
use App\Entity\Core\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

use Doctrine\ORM\ORMException;
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
     * @var int
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

    /**
     * @var array
     */
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
        } catch (\PDOException $e) {
            $this->userErrors['userErrors'] = $e->getMessage();
        }  catch (ORMException $e) {
            $this->userErrors['userErrors'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->userErrors['userErrors'] = $e->getMessage();
        }
    }

    /**
     * @throws \Exception
     */
    private function setUserVariables(): void
    {
        $this->userID = $this->user->getUser()->getUserid();
        $this->groupNameIDs = $this->groupNameIDs = $this->em->getRepository(GroupMapping::class)->getGroupsForUser($this->userID);
        $this->roles =  $this->user->getUser()->getRoles();

        if (!$this->groupNameIDs || !$this->userID || empty($this->roles)) {
            throw new \Exception("The User Variables Cannot be set Please try again");
        }
    }

    protected function getGroupNameID(): ?array
    {
        return $this->groupNameIDs;
    }

    public function getUserID(): ?int
    {
        return $this->userID;
    }

    public function getUserRoles(): ?array
    {
        return $this->roles;
    }

    public function getUserErrors()
    {
        return $this->userErrors;
    }

}