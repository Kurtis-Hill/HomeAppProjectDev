<?php


namespace App\Services;

use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\Room;
use App\Entity\Core\User;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\SensorType;
use App\Entity\Sensors\SensorTypes\Bmp;
use App\Entity\Sensors\SensorTypes\Dallas;
use App\Entity\Sensors\SensorTypes\Dht;
use App\Entity\Sensors\SensorTypes\Soil;
use App\Form\CardViewForms\StandardSensorOutOFBoundsForm;
use App\Form\SensorForms\UpdateReadingForm;
use App\HomeAppSensorCore\Interfaces\APIErrorInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractHomeAppUserSensorServiceCore implements APIErrorInterface
{
    /**
     * @var ?UserInterface
     */
    private ?UserInterface $user;

    /**
     * @var EntityManager|EntityManagerInterface
     */
    protected EntityManager|EntityManagerInterface $em;

    /**
     * @var int|null
     */
    private int|null $userID;

    /**
     * @var array
     */
    private array $groupNameDetails = [];

    /**
     * @var array
     */
    protected array $fatalErrors = [];

    /**
     * @var array
     */
    protected array $serverErrors = [];

    /**
     * @var array
     */
    protected array $userInputErrors = [];

    /**
     * HomeAppRoomAbstract constructor.
     * @param EntityManagerInterface $em
     * @param Security $security
     *
     */
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->user = $security->getUser();

        try {
            $this->setUserVariables();
        } catch (\Exception | \RuntimeException $e) {
            $this->fatalErrors[] = $e->getMessage();
        }
    }

    /**
     * @throws \Exception
     */
    private function setUserVariables()
    {
        $userCredentials = [$this->user, 'getUserID'];

        if (is_callable($userCredentials, true)) {
            $this->userID = $this->user->getUserID();
            $this->groupNameDetails = $this->em->getRepository(GroupnNameMapping::class)->getGroupsForUser($this->userID);
            if (empty($this->groupNameDetails)) {
                throw new \RuntimeException('The User Variables Cannot be set Please try again');
            }
        } else {
            throw new \RuntimeException('Could not find user');
        }
    }

    /**
     * @return array
     */
    #[Pure] public function getGroupNameIDs()
    {
        return array_column($this->groupNameDetails, 'groupNameID');
    }

    /**
     * @return array
     */
    protected function getGroupNameDetails()
    {
        return $this->groupNameDetails;
    }

    /**
     * @return int|null
     */
    protected function getUserID()
    {
        return $this->userID;
    }

    /**
     * @return UserInterface|null
     */
    protected function getUser(): ?UserInterface
    {
        return $this->user;
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
    public function getUserInputErrors(): array
    {
        return $this->userInputErrors;
    }

    /**
     * @return array
     */
    public function getServerErrors(): array
    {
        return $this->serverErrors;
    }

}
