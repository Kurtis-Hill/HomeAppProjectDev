<?php


namespace App\Services;


use App\Entity\Core\Room;
use App\Entity\Core\User;
use App\Entity\Devices\Devices;
use App\HomeAppSensorCore\Interfaces\APIErrorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Security\Core\Security;


class UserInterfaceService implements APIErrorInterface
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
    private array $userRooms;

    /**
     * @var array
     */
    private array $userDevices;

    /**
     * @var array
     */
    private array $userInputErrors = [];

    /**
     * @var array
     */
    private array $serverErrors = [];

    /**
     * @var array
     */
    private array $fatalErrors = [];

    /**
     * UserInterfaceService constructor.
     * @param EntityManagerInterface $em
     * @param Security $security
     */
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->user = $security->getUser();

        try {
            if ($this->user instanceof User) {
                $this->userRooms = $this->em->getRepository(Room::class)->getAllUserRoomsByGroupId($this->user->getGroupNameIDs());
                $this->userDevices = $this->em->getRepository(Devices::class)->getAllUsersDevicesByGroupId($this->user->getGroupNameAndIds());
            }
            else {
                throw new BadRequestException('This type of user is not expected');
            }
        } catch (BadRequestException $exception) {
            $this->userInputErrors[] = $exception->getMessage();
        } catch (\RuntimeException $exception) {
            $this->serverErrors[] = $exception->getMessage();
        } catch (ORMException $exception) {
            $this->serverErrors[] = 'Query Failed';
        } catch (\Exception $exception) {
            $this->fatalErrors[] = 'Something Happened Please log what you were doing and send a crash report';
        }
    }

    #[ArrayShape(['rooms' => [], 'devices' => [], 'groupNames' => []])]
    public function getNavBarData(): array
    {
        return  [
            'rooms' => $this->userRooms,
            'devices' => $this->userDevices,
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
    public function getFatalErrors(): array
    {
        return $this->fatalErrors;
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
}
