<?php


namespace App\HomeAppCore;

use App\Entity\Core\GroupnNameMapping;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Security;

abstract class HomeAppCoreAbstract
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

    /**
     * @var array
     */
    protected $userErrors = [];

    /**
     * HomeAppRoomAbstract constructor.
     */
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->user = $security;

        try {
            $this->setUserVariables();
        } catch (\Exception $e) {
            $this->userErrors[] = $e->getMessage();

            return new RedirectResponse('/HomeApp/logout');
        }
    }

    /**
     * @return bool|FormInterface
     */
    public function processForm(FormInterface $form, array $formData)
    {
        $form->submit($formData);

        if ($form->isSubmitted() && $form->isValid()) {
            $validFormData = $form->getData();

            try {
                $this->em->persist($validFormData);
            } catch (\PDOException | \Exception $e) {
                error_log($e->getMessage());
            }

            return true;
        }

        return $form;
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
        return $this->userErrors;
    }

    /**
     * @throws \Exception
     */
    private function setUserVariables()
    {
        $this->userID = $this->user->getUser()->getUserID();
        $this->groupNameIDs = $this->groupNameIDs = $this->em->getRepository(GroupnNameMapping::class)->getGroupsForUser($this->userID);
        $this->roles = $this->user->getUser()->getRoles();

//        dd($this->groupNameIDs, $this->userID,$this->roles, 'heyy');
        if (!$this->groupNameIDs || !$this->userID || empty($this->roles)) {
            throw new \Exception('The User Variables Cannot be set Please try again');
        }
    }
}
