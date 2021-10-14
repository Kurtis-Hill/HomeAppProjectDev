<?php


namespace App\Services;


use App\Core\APIInterface\APIErrorInterface;
use App\Devices\Entity\Devices;
use App\Entity\Core\Room;
use App\Entity\Core\User;
use App\HomeAppSensorCore\Interfaces\Services\LoggedInUserRequiredInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Security\Core\Security;


class UserInterfaceService implements APIErrorInterface, LoggedInUserRequiredInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var User|null
     */
    private ?User $user;

    /**
     * @var array
     */
    private array $userInputErrors = [];

    /**
     * @var array
     */
    private array $serverErrors = [];


    /**
     * UserInterfaceService constructor.
     * @param EntityManagerInterface $em
     * @param Security $security
     */
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->user = $security->getUser();
    }

    #[ArrayShape(['rooms' => [], 'devices' => [], 'groupNames' => []])]
    public function getNavBarData(): array
    {
        try {
            $userRooms = $this->em->getRepository(Room::class)->getAllUserRoomsByGroupId($this->getUser()?->getGroupNameIDs());
            $userDevices = $this->em->getRepository(Devices::class)->getAllUsersDevicesByGroupId($this->getUser()?->getGroupNameAndIds());
        } catch (ORMException $exception) {
            error_log($exception);
            $this->serverErrors[] = 'NavBar Data Query Failed';
        }

        return  [
            'rooms' => $userRooms ?? [],
            'devices' => $userDevices ?? [],
            'groupNames' => $this->user->getGroupNameAndIds()
        ];
    }


    #[ArrayShape(['userID' => "int", 'roles' => "array|string[]"])]
    public function getAppUserDataForLocalStorage()
    {
        return [
            'userID' => $this->user->getUserID(),
            'roles' => $this->user->getRoles(),
        ];
    }

    /**
     * @return array
     */
    public function getServerErrors(): array
    {
        return $this->serverErrors;
    }

    /**
     * @return array
     */
    public function getUserInputErrors(): array
    {
        return $this->userInputErrors;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}
