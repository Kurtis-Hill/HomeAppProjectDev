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
    private Security $user;

    /**
     * @var EntityManager|EntityManagerInterface
     */
    protected EntityManager|EntityManagerInterface $em;

    /**
     * @var int
     */
    private int $userID;

    /**
     * @var array
     */
    private array $roles;

    /**
     * @var array
     */
    private array $groupNameIDs = [];

    /**
     * @var array
     */
    protected array $userErrors = [];

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
     * @param FormInterface $form
     * @param array $formData
     * @return bool|FormInterface
     */
    public function processForm(FormInterface $form, array $formData): bool|FormInterface
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

    public function getGroupNameIDs()
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


        if (!$this->groupNameIDs || !$this->userID || empty($this->roles)) {
            throw new \Exception('The User Variables Cannot be set Please try again');
        }
    }
}
