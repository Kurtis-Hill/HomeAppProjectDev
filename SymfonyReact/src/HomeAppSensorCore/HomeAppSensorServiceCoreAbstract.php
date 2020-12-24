<?php


namespace App\HomeAppSensorCore;

use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\Room;
use App\Entity\Sensors\Devices;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;

abstract class HomeAppSensorServiceCoreAbstract
{
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
    private array $groupNameDetails = [];

    /**
     * @var array
     */
    protected array $userErrors = [];

    /**
     * @var array
     */
    private array $usersRooms = [];

    /**
     * @var array
     */
    private array $devices = [];

    /**
     * @var Security
     */
    private Security $user;

    /**
     * @var EntityManager|EntityManagerInterface
     */
    protected EntityManager|EntityManagerInterface $em;


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

    #[Pure] public function getGroupNameIDs()
    {
        return array_keys($this->groupNameDetails);
    }

    public function getGroupNameDetails()
    {
        return $this->groupNameDetails;
    }

    public function getUserID()
    {
        return $this->userID;
    }

    public function getUserRoles()
    {
        return $this->roles;
    }

    /**
     * @return array
     */
    public function getUsersRooms(): array
    {
        return $this->usersRooms;
    }

    /**
     * @return array
     */
    public function getUsersDevices(): array
    {
        return $this->devices;
    }

    public function getUserErrors(): array
    {
        return $this->userErrors;
    }

    /**
     * @throws \Exception
     */
    private function setUserVariables()
    {
        $this->userID = $this->user->getUser()->getUserID();
        $this->groupNameDetails = $this->groupNameDetails = $this->em->getRepository(GroupnNameMapping::class)->getGroupsForUser($this->userID);
        $this->roles = $this->user->getUser()->getRoles();
        $this->devices = $this->em->getRepository(Devices::class)->getAllUsersDevices($this->getGroupNameIDs());
        $this->usersRooms = $this->em->getRepository(Room::class)->getRoomsForUser($this->getGroupNameIDs());


        if (!$this->groupNameDetails || !$this->userID || empty($this->roles || $this->devices || $this->userRooms)) {
            throw new \Exception('The User Variables Cannot be set Please try again');
        }
    }
}
